<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesEditorUpload;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PageController extends Controller
{
    use HandlesEditorUpload;

    public function index(Request $request): View
    {
        $pages = Page::query()
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when($request->filled('menu_group'), fn ($q) => $q->where('menu_group', $request->menu_group))
            ->when($request->filled('is_published'), fn ($q) => $q->where('is_published', $request->boolean('is_published')))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin.pages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePage($request);

        $isPublished = $request->boolean('is_published', true);

        Page::create([
            ...$validated,
            'sort_order' => $validated['sort_order'] ?? 0,
            'slug' => Page::uniqueSlug($validated['title'], $validated['menu_group']),
            'user_id' => Auth::id(),
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        return redirect()
            ->route('admin.halaman.index')
            ->with('success', 'Halaman berhasil ditambahkan.');
    }

    public function edit(Page $halaman): View
    {
        return view('admin.pages.edit', ['page' => $halaman]);
    }

    public function update(Request $request, Page $halaman): RedirectResponse
    {
        $validated = $this->validatePage($request);

        $isPublished = $request->boolean('is_published');
        $slug = $halaman->slug;

        if ($halaman->title !== $validated['title']) {
            $slug = Page::uniqueSlug($validated['title'], $validated['menu_group'], $halaman->id);
        } elseif ($halaman->menu_group !== $validated['menu_group']) {
            $slug = Page::uniqueSlug($validated['title'], $validated['menu_group'], $halaman->id);
        }

        $halaman->update([
            ...$validated,
            'slug' => $slug,
            'is_published' => $isPublished,
            'published_at' => $isPublished
                ? ($halaman->published_at ?? now())
                : null,
        ]);

        return redirect()
            ->route('admin.halaman.index')
            ->with('success', 'Halaman berhasil diperbarui.');
    }

    public function destroy(Page $halaman): RedirectResponse
    {
        $halaman->delete();

        return redirect()
            ->route('admin.halaman.index')
            ->with('success', 'Halaman berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePage(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'menu_group' => ['required', 'string', 'in:'.implode(',', array_keys(Page::MENU_GROUPS))],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ], [], [
            'title' => 'judul',
            'menu_group' => 'grup menu',
            'excerpt' => 'ringkasan',
            'content' => 'konten',
            'sort_order' => 'urutan',
        ]);
    }
}
