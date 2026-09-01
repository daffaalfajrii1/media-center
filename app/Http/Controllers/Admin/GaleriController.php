<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use App\Models\GaleriItem;
use App\Support\Youtube;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GaleriController extends Controller
{
    public function index(Request $request): View
    {
        $galeris = Galeri::query()
            ->with('author')
            ->withCount([
                'items as photos_count' => fn ($q) => $q->where('type', GaleriItem::TYPE_PHOTO),
                'items as videos_count' => fn ($q) => $q->where('type', GaleriItem::TYPE_YOUTUBE),
            ])
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.galeri.index', compact('galeris'));
    }

    public function create(): View
    {
        return view('admin.galeri.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateGaleri($request);
        $status = $request->input('status', Galeri::STATUS_DRAFT);

        $galeri = Galeri::create([
            ...$validated,
            'slug' => Galeri::uniqueSlug($validated['title']),
            'user_id' => Auth::id(),
            'status' => $status,
            'published_at' => $status === Galeri::STATUS_PUBLISHED ? now() : null,
            'thumbnail' => $this->storeThumbnail($request),
        ]);

        return redirect()
            ->route('admin.galeri.edit', $galeri)
            ->with('success', 'Album galeri berhasil dibuat. Tambahkan foto atau video YouTube di bawah.');
    }

    public function edit(Galeri $galeri): View
    {
        $galeri->load('items');

        return view('admin.galeri.edit', compact('galeri'));
    }

    public function update(Request $request, Galeri $galeri): RedirectResponse
    {
        $validated = $this->validateGaleri($request);
        $status = $request->input('status', Galeri::STATUS_DRAFT);

        $slug = $galeri->slug;
        if ($galeri->title !== $validated['title']) {
            $slug = Galeri::uniqueSlug($validated['title'], $galeri->id);
        }

        $data = [
            ...$validated,
            'slug' => $slug,
            'status' => $status,
            'published_at' => $status === Galeri::STATUS_PUBLISHED
                ? ($galeri->published_at ?? now())
                : null,
        ];

        if ($request->boolean('remove_thumbnail') && $galeri->thumbnail) {
            Storage::disk('public')->delete($galeri->thumbnail);
            $data['thumbnail'] = null;
        } elseif ($request->hasFile('thumbnail')) {
            if ($galeri->thumbnail) {
                Storage::disk('public')->delete($galeri->thumbnail);
            }
            $data['thumbnail'] = $this->storeThumbnail($request);
        }

        $galeri->update($data);

        return redirect()
            ->route('admin.galeri.edit', $galeri)
            ->with('success', 'Album galeri berhasil diperbarui.');
    }

    public function destroy(Galeri $galeri): RedirectResponse
    {
        $galeri->load('items');
        $this->deleteGaleriFiles($galeri);
        $galeri->delete();

        return redirect()
            ->route('admin.galeri.index')
            ->with('success', 'Album galeri berhasil dihapus.');
    }

    public function storePhotos(Request $request, Galeri $galeri): RedirectResponse
    {
        $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['image', 'max:5120'],
            'titles' => ['nullable', 'array'],
            'titles.*' => ['nullable', 'string', 'max:255'],
        ], [], [
            'photos' => 'foto',
            'photos.*' => 'foto',
        ]);

        $maxSort = (int) $galeri->items()->max('sort_order');
        $uploaded = 0;

        foreach ($request->file('photos', []) as $index => $file) {
            $path = $file->store("galeris/{$galeri->id}/photos", 'public');
            $maxSort++;

            GaleriItem::create([
                'galeri_id' => $galeri->id,
                'type' => GaleriItem::TYPE_PHOTO,
                'title' => $request->input("titles.{$index}") ?: null,
                'image_path' => $path,
                'sort_order' => $maxSort,
            ]);

            $uploaded++;
        }

        return redirect()
            ->route('admin.galeri.edit', $galeri)
            ->with('success', "{$uploaded} foto berhasil ditambahkan.");
    }

    public function storeYoutube(Request $request, Galeri $galeri): RedirectResponse
    {
        $validated = $request->validate([
            'youtube_url' => ['required', 'url', 'max:500'],
            'title' => ['nullable', 'string', 'max:255'],
        ], [], [
            'youtube_url' => 'tautan YouTube',
            'title' => 'judul video',
        ]);

        $videoId = Youtube::extractVideoId($validated['youtube_url']);
        if (! $videoId) {
            return back()
                ->withInput()
                ->withErrors(['youtube_url' => 'Tautan YouTube tidak valid.']);
        }

        $maxSort = (int) $galeri->items()->max('sort_order');

        GaleriItem::create([
            'galeri_id' => $galeri->id,
            'type' => GaleriItem::TYPE_YOUTUBE,
            'title' => $validated['title'] ?? null,
            'youtube_url' => $validated['youtube_url'],
            'youtube_video_id' => $videoId,
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()
            ->route('admin.galeri.edit', $galeri)
            ->with('success', 'Video YouTube berhasil ditambahkan.');
    }

    public function destroyItem(Galeri $galeri, GaleriItem $item): RedirectResponse
    {
        if ($item->galeri_id !== $galeri->id) {
            abort(404);
        }

        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        return redirect()
            ->route('admin.galeri.edit', $galeri)
            ->with('success', 'Media berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateGaleri(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'event_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in([Galeri::STATUS_DRAFT, Galeri::STATUS_PUBLISHED])],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ], [], [
            'title' => 'judul album',
            'description' => 'deskripsi',
            'event_date' => 'tanggal kegiatan',
            'status' => 'status',
            'is_featured' => 'sorotan',
            'sort_order' => 'urutan',
            'thumbnail' => 'gambar sampul',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        return $validated;
    }

    private function storeThumbnail(Request $request): ?string
    {
        if (! $request->hasFile('thumbnail')) {
            return null;
        }

        return $request->file('thumbnail')->store('galeris/thumbnails', 'public');
    }

    private function deleteGaleriFiles(Galeri $galeri): void
    {
        if ($galeri->thumbnail) {
            Storage::disk('public')->delete($galeri->thumbnail);
        }

        foreach ($galeri->items as $item) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
        }
    }
}
