<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesEditorUpload;
use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\PengumumanAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PengumumanController extends Controller
{
    use HandlesEditorUpload {
        uploadEditorImage as traitUploadEditorImage;
    }

    public function index(Request $request): View
    {
        $pengumumans = Pengumuman::query()
            ->with('author')
            ->withCount('attachments')
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->priority))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pengumuman.index', compact('pengumumans'));
    }

    public function create(): View
    {
        return view('admin.pengumuman.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePengumuman($request);
        $status = $request->input('status', Pengumuman::STATUS_DRAFT);

        $pengumuman = Pengumuman::create([
            ...$validated,
            'slug' => Pengumuman::uniqueSlug($validated['title']),
            'user_id' => Auth::id(),
            'status' => $status,
            'published_at' => $status === Pengumuman::STATUS_PUBLISHED ? now() : null,
        ]);

        return redirect()
            ->route('admin.pengumuman.edit', $pengumuman)
            ->with('success', 'Pengumuman berhasil dibuat. Tambahkan file PDF unduhan di bawah.');
    }

    public function edit(Pengumuman $pengumuman): View
    {
        $pengumuman->load('attachments');

        return view('admin.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, Pengumuman $pengumuman): RedirectResponse
    {
        $validated = $this->validatePengumuman($request);
        $status = $request->input('status', Pengumuman::STATUS_DRAFT);

        $slug = $pengumuman->slug;
        if ($pengumuman->title !== $validated['title']) {
            $slug = Pengumuman::uniqueSlug($validated['title'], $pengumuman->id);
        }

        $pengumuman->update([
            ...$validated,
            'slug' => $slug,
            'status' => $status,
            'published_at' => $status === Pengumuman::STATUS_PUBLISHED
                ? ($pengumuman->published_at ?? now())
                : null,
        ]);

        return redirect()
            ->route('admin.pengumuman.edit', $pengumuman)
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman): RedirectResponse
    {
        $pengumuman->load('attachments');

        foreach ($pengumuman->attachments as $attachment) {
            $attachment->deleteFile();
        }

        $pengumuman->delete();

        return redirect()
            ->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function storeAttachments(Request $request, Pengumuman $pengumuman): RedirectResponse
    {
        $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'mimes:pdf', 'max:10240'],
            'titles' => ['nullable', 'array'],
            'titles.*' => ['nullable', 'string', 'max:255'],
        ], [], [
            'files' => 'file PDF',
            'files.*' => 'file PDF',
        ]);

        $maxSort = (int) $pengumuman->attachments()->max('sort_order');
        $uploaded = 0;

        foreach ($request->file('files', []) as $index => $file) {
            $path = $file->store("pengumumans/{$pengumuman->id}/pdf", 'public');
            $maxSort++;

            PengumumanAttachment::create([
                'pengumuman_id' => $pengumuman->id,
                'title' => $request->input("titles.{$index}") ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'sort_order' => $maxSort,
            ]);

            $uploaded++;
        }

        return redirect()
            ->route('admin.pengumuman.edit', $pengumuman)
            ->with('success', "{$uploaded} file PDF berhasil ditambahkan.");
    }

    public function destroyAttachment(Pengumuman $pengumuman, PengumumanAttachment $attachment): RedirectResponse
    {
        if ($attachment->pengumuman_id !== $pengumuman->id) {
            abort(404);
        }

        $attachment->deleteFile();
        $attachment->delete();

        return redirect()
            ->route('admin.pengumuman.edit', $pengumuman)
            ->with('success', 'File PDF berhasil dihapus.');
    }

    public function uploadEditorImage(Request $request)
    {
        $request->merge(['folder' => 'pengumumans/editor']);

        return $this->traitUploadEditorImage($request);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePengumuman(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_keys(Pengumuman::STATUSES))],
            'priority' => ['required', Rule::in(array_keys(Pengumuman::PRIORITIES))],
            'valid_until' => ['nullable', 'date'],
            'is_featured' => ['nullable', 'boolean'],
        ], [], [
            'title' => 'judul',
            'excerpt' => 'ringkasan',
            'content' => 'isi pengumuman',
            'status' => 'status',
            'priority' => 'prioritas',
            'valid_until' => 'berlaku sampai',
            'is_featured' => 'sorotan',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');

        return $validated;
    }
}
