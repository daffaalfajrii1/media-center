<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\LayananPengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LayananPengajuanController extends Controller
{
    public function index(Request $request): View
    {
        $pengajuans = LayananPengajuan::query()
            ->with(['layanan', 'processor'])
            ->when($request->search, fn ($q, $search) => $q->where('registration_number', 'like', "%{$search}%"))
            ->when($request->filled('layanan_id'), fn ($q) => $q->where('layanan_id', $request->layanan_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('submitted_at')
            ->paginate(20)
            ->withQueryString();

        $layanans = Layanan::query()->orderBy('title')->get(['id', 'title', 'code']);

        return view('admin.layanan-pengajuan.index', compact('pengajuans', 'layanans'));
    }

    public function show(LayananPengajuan $layananPengajuan): View
    {
        $layananPengajuan->load(['layanan.fields', 'processor']);

        return view('admin.layanan-pengajuan.show', [
            'pengajuan' => $layananPengajuan,
        ]);
    }

    public function updateStatus(Request $request, LayananPengajuan $layananPengajuan): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(LayananPengajuan::STATUSES))],
            'admin_note' => ['nullable', 'string', 'max:2000'],
            'completion_method' => ['nullable', Rule::in(array_keys(LayananPengajuan::COMPLETION_METHODS))],
            'completion_message' => ['nullable', 'string', 'max:2000'],
            'result_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [], [
            'status' => 'status',
            'admin_note' => 'catatan admin',
            'completion_method' => 'cara penyelesaian',
            'completion_message' => 'pesan untuk pemohon',
            'result_file' => 'file hasil',
        ]);

        if ($validated['status'] === LayananPengajuan::STATUS_COMPLETED) {
            if (empty($validated['completion_method'])) {
                return back()
                    ->withInput()
                    ->withErrors(['completion_method' => 'Pilih cara penyelesaian saat status Selesai.']);
            }

            if ($validated['completion_method'] === LayananPengajuan::COMPLETION_FILE_UPLOAD) {
                if (! $request->hasFile('result_file') && ! $layananPengajuan->result_file_path) {
                    return back()
                        ->withInput()
                        ->withErrors(['result_file' => 'Upload file hasil layanan (PDF atau gambar).']);
                }
            }

            if ($validated['completion_method'] === LayananPengajuan::COMPLETION_OFFICE_PICKUP) {
                if (empty($validated['completion_message'])) {
                    return back()
                        ->withInput()
                        ->withErrors(['completion_message' => 'Isi instruksi pengambilan di kantor untuk pemohon.']);
                }
            }
        }

        $data = [
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? null,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ];

        if ($validated['status'] === LayananPengajuan::STATUS_COMPLETED) {
            $data['completion_method'] = $validated['completion_method'];
            $data['completion_message'] = $validated['completion_message'] ?? null;

            if ($request->hasFile('result_file')) {
                if ($layananPengajuan->result_file_path) {
                    Storage::disk('public')->delete($layananPengajuan->result_file_path);
                }
                $data['result_file_path'] = $request->file('result_file')
                    ->store("layanan-pengajuans/{$layananPengajuan->id}/hasil", 'public');
            }
        } else {
            $data['completion_method'] = null;
            $data['completion_message'] = null;

            if ($layananPengajuan->result_file_path) {
                Storage::disk('public')->delete($layananPengajuan->result_file_path);
                $data['result_file_path'] = null;
            }
        }

        $layananPengajuan->update($data);

        return redirect()
            ->route('admin.layanan-pengajuan.show', $layananPengajuan)
            ->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
