<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Pegawai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PegawaiController extends Controller
{
    public function index(Request $request): View
    {
        $pegawais = Pegawai::query()
            ->with('jabatan')
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->filled('jabatan_id'), fn ($q) => $q->where('jabatan_id', $request->jabatan_id))
            ->when($request->status === 'published', fn ($q) => $q->published())
            ->when($request->status === 'draft', fn ($q) => $q->where('is_published', false))
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        $jabatans = Jabatan::query()->active()->ordered()->get();

        return view('admin.pegawai.index', compact('pegawais', 'jabatans'));
    }

    public function create(): View
    {
        return view('admin.pegawai.create', [
            'jabatans' => Jabatan::query()->active()->ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePegawai($request);

        Pegawai::create([
            ...$validated,
            'photo_path' => $this->storePhoto($request),
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()
            ->route('admin.pegawai.index')
            ->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit(Pegawai $pegawai): View
    {
        return view('admin.pegawai.edit', [
            'pegawai' => $pegawai,
            'jabatans' => Jabatan::query()->ordered()->get(),
        ]);
    }

    public function update(Request $request, Pegawai $pegawai): RedirectResponse
    {
        $validated = $this->validatePegawai($request);

        $data = [
            ...$validated,
            'is_published' => $request->boolean('is_published'),
        ];

        if ($request->boolean('remove_photo') && $pegawai->photo_path) {
            Storage::disk('public')->delete($pegawai->photo_path);
            $data['photo_path'] = null;
        } elseif ($request->hasFile('photo')) {
            if ($pegawai->photo_path) {
                Storage::disk('public')->delete($pegawai->photo_path);
            }
            $data['photo_path'] = $this->storePhoto($request);
        }

        $pegawai->update($data);

        return redirect()
            ->route('admin.pegawai.index')
            ->with('success', 'Pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai): RedirectResponse
    {
        if ($pegawai->photo_path) {
            Storage::disk('public')->delete($pegawai->photo_path);
        }

        $pegawai->delete();

        return redirect()
            ->route('admin.pegawai.index')
            ->with('success', 'Pegawai berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validatePegawai(Request $request): array
    {
        return $request->validate([
            'jabatan_id' => ['required', 'exists:jabatans,id'],
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ], [], [
            'jabatan_id' => 'jabatan',
            'name' => 'nama pegawai',
            'nip' => 'NIP',
            'photo' => 'foto pegawai',
        ]);
    }

    private function storePhoto(Request $request): ?string
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        return $request->file('photo')->store('pegawais/photos', 'public');
    }
}
