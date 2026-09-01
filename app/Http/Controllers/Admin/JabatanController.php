<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JabatanController extends Controller
{
    public function index(): View
    {
        $jabatans = Jabatan::query()
            ->withCount('pegawais')
            ->ordered()
            ->paginate(15);

        return view('admin.jabatan.index', compact('jabatans'));
    }

    public function create(): View
    {
        return view('admin.jabatan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [], ['name' => 'nama jabatan']);

        Jabatan::create([
            'name' => $validated['name'],
            'slug' => Jabatan::uniqueSlug($validated['name']),
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(Jabatan $jabatan): View
    {
        return view('admin.jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, Jabatan $jabatan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [], ['name' => 'nama jabatan']);

        $slug = $jabatan->slug;
        if ($jabatan->name !== $validated['name']) {
            $slug = Jabatan::uniqueSlug($validated['name'], $jabatan->id);
        }

        $jabatan->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Jabatan $jabatan): RedirectResponse
    {
        if ($jabatan->pegawais()->exists()) {
            return redirect()
                ->route('admin.jabatan.index')
                ->with('error', 'Jabatan tidak dapat dihapus karena masih memiliki pegawai.');
        }

        $jabatan->delete();

        return redirect()
            ->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
