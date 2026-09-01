<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\LayananPengajuan;
use App\Support\LayananPengajuanForm;
use App\Support\LayananRegistrationNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LayananController extends Controller
{
    public function index(): View
    {
        $layanans = Layanan::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('public.layanan.index', compact('layanans'));
    }

    public function show(string $slug): View
    {
        $layanan = Layanan::query()
            ->active()
            ->with(['fields' => fn ($q) => $q->orderBy('sort_order')])
            ->where('slug', $slug)
            ->firstOrFail();

        $allLayanans = Layanan::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get(['id', 'title', 'slug']);

        return view('public.layanan.show', compact('layanan', 'allLayanans'));
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $layanan = Layanan::query()
            ->active()
            ->with(['fields' => fn ($q) => $q->orderBy('sort_order')])
            ->where('slug', $slug)
            ->firstOrFail();

        $validation = LayananPengajuanForm::validation($layanan);
        $request->validate(
            $validation['rules'],
            $validation['messages'],
            $validation['attributes'],
        );

        $data = LayananPengajuanForm::collectData($layanan, $request);

        $pengajuan = LayananPengajuan::create([
            'layanan_id' => $layanan->id,
            'registration_number' => LayananRegistrationNumber::generate($layanan),
            'data' => $data,
            'status' => LayananPengajuan::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('public.layanan.check-status', ['nomor' => $pengajuan->registration_number])
            ->with('success', 'Pengajuan berhasil dikirim. Simpan nomor registrasi Anda: '.$pengajuan->registration_number);
    }

    public function checkStatus(Request $request): View
    {
        $pengajuan = null;

        if ($request->filled('nomor')) {
            $pengajuan = LayananPengajuan::query()
                ->with(['layanan.fields'])
                ->where('registration_number', $request->nomor)
                ->first();
        }

        return view('public.layanan.check-status', compact('pengajuan'));
    }

    public function lookupStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'nomor' => ['required', 'string', 'max:50'],
        ], [], ['nomor' => 'nomor registrasi']);

        return redirect()->route('public.layanan.check-status', ['nomor' => $request->nomor]);
    }
}
