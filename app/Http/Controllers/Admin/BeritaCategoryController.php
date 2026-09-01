<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BeritaCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BeritaCategoryController extends Controller
{
    public function index(): View
    {
        $categories = BeritaCategory::query()
            ->withCount('beritas')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.berita-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.berita-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ], [], ['name' => 'nama kategori']);

        BeritaCategory::create([
            'name' => $validated['name'],
            'slug' => BeritaCategory::uniqueSlug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.berita-kategori.index')
            ->with('success', 'Kategori berita berhasil ditambahkan.');
    }

    public function edit(BeritaCategory $beritaKategori): View
    {
        return view('admin.berita-categories.edit', ['category' => $beritaKategori]);
    }

    public function update(Request $request, BeritaCategory $beritaKategori): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ], [], ['name' => 'nama kategori']);

        $slug = $beritaKategori->slug;
        if ($beritaKategori->name !== $validated['name']) {
            $slug = BeritaCategory::uniqueSlug($validated['name'], $beritaKategori->id);
        }

        $beritaKategori->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.berita-kategori.index')
            ->with('success', 'Kategori berita berhasil diperbarui.');
    }

    public function destroy(BeritaCategory $beritaKategori): RedirectResponse
    {
        if ($beritaKategori->beritas()->exists()) {
            return redirect()
                ->route('admin.berita-kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki berita.');
        }

        $beritaKategori->delete();

        return redirect()
            ->route('admin.berita-kategori.index')
            ->with('success', 'Kategori berita berhasil dihapus.');
    }
}
