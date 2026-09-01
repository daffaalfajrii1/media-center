<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesEditorUpload;
use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\BeritaCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BeritaController extends Controller
{
    use HandlesEditorUpload {
        uploadEditorImage as traitUploadEditorImage;
    }

    public function index(Request $request): View
    {
        $beritas = Berita::query()
            ->with(['category', 'author'])
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('berita_category_id'), fn ($q) => $q->where('berita_category_id', $request->berita_category_id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = BeritaCategory::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.berita.index', compact('beritas', 'categories'));
    }

    public function create(): View
    {
        $categories = BeritaCategory::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.berita.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateBerita($request);
        $status = $request->input('status', Berita::STATUS_DRAFT);

        Berita::create([
            ...$validated,
            'berita_category_id' => $this->resolveCategoryId($request),
            'slug' => Berita::uniqueSlug($validated['title']),
            'user_id' => Auth::id(),
            'status' => $status,
            'published_at' => $status === Berita::STATUS_PUBLISHED ? now() : null,
            'thumbnail' => $this->storeThumbnail($request),
        ]);

        return redirect()
            ->route('admin.berita.index')
            ->with('success', $this->successMessage($status, false));
    }

    public function edit(Berita $berita): View
    {
        $categories = BeritaCategory::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.berita.edit', compact('berita', 'categories'));
    }

    public function update(Request $request, Berita $berita): RedirectResponse
    {
        $validated = $this->validateBerita($request);
        $status = $request->input('status', Berita::STATUS_DRAFT);

        $slug = $berita->slug;
        if ($berita->title !== $validated['title']) {
            $slug = Berita::uniqueSlug($validated['title'], $berita->id);
        }

        $data = [
            'berita_category_id' => $this->resolveCategoryId($request),
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? null,
            'slug' => $slug,
            'status' => $status,
            'published_at' => $status === Berita::STATUS_PUBLISHED
                ? ($berita->published_at ?? now())
                : null,
        ];

        if ($request->boolean('remove_thumbnail') && $berita->thumbnail) {
            Storage::disk('public')->delete($berita->thumbnail);
            $data['thumbnail'] = null;
        } elseif ($request->hasFile('thumbnail')) {
            if ($berita->thumbnail) {
                Storage::disk('public')->delete($berita->thumbnail);
            }
            $data['thumbnail'] = $this->storeThumbnail($request);
        }

        $berita->update($data);

        return redirect()
            ->route('admin.berita.edit', $berita)
            ->with('success', $this->successMessage($status, true));
    }

    public function destroy(Berita $berita): RedirectResponse
    {
        if ($berita->thumbnail) {
            Storage::disk('public')->delete($berita->thumbnail);
        }

        $berita->delete();

        return redirect()
            ->route('admin.berita.index')
            ->with('success', 'Berita berhasil dihapus.');
    }

    public function uploadEditorImage(Request $request)
    {
        $request->merge(['folder' => 'beritas/editor']);

        return $this->traitUploadEditorImage($request);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateBerita(Request $request): array
    {
        return $request->validate([
            'berita_category_id' => ['nullable', 'exists:berita_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'in:'.Berita::STATUS_DRAFT.','.Berita::STATUS_PUBLISHED],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ], [], [
            'berita_category_id' => 'kategori',
            'title' => 'judul',
            'excerpt' => 'ringkasan',
            'content' => 'konten',
            'status' => 'status',
            'thumbnail' => 'gambar utama',
        ]);
    }

    private function resolveCategoryId(Request $request): ?int
    {
        $categoryId = $request->input('berita_category_id');

        return filled($categoryId) ? (int) $categoryId : null;
    }

    private function successMessage(string $status, bool $updated): string
    {
        if ($status === Berita::STATUS_PUBLISHED) {
            return $updated
                ? 'Berita berhasil diperbarui dan diterbitkan.'
                : 'Berita berhasil ditambahkan dan diterbitkan.';
        }

        return $updated
            ? 'Berita berhasil disimpan sebagai draft.'
            : 'Berita berhasil ditambahkan sebagai draft.';
    }

    private function storeThumbnail(Request $request): ?string
    {
        if (! $request->hasFile('thumbnail')) {
            return null;
        }

        return $request->file('thumbnail')->store('beritas/thumbnails', 'public');
    }
}
