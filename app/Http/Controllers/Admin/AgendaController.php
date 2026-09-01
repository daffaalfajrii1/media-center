<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesEditorUpload;
use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AgendaController extends Controller
{
    use HandlesEditorUpload {
        uploadEditorImage as traitUploadEditorImage;
    }

    public function index(Request $request): View
    {
        $agendas = Agenda::query()
            ->with('author')
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('event_type'), fn ($q) => $q->where('event_type', $request->event_type))
            ->when($request->period === 'upcoming', fn ($q) => $q->upcoming())
            ->when($request->period === 'past', fn ($q) => $q->past())
            ->when(
                ! $request->filled('period'),
                fn ($q) => $q->orderByDesc('event_date')->orderByDesc('start_time')
            )
            ->paginate(15)
            ->withQueryString();

        return view('admin.agenda.index', compact('agendas'));
    }

    public function create(): View
    {
        return view('admin.agenda.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAgenda($request);
        $status = $request->input('status', Agenda::STATUS_DRAFT);

        Agenda::create([
            ...$validated,
            'slug' => Agenda::uniqueSlug($validated['title']),
            'user_id' => Auth::id(),
            'status' => $status,
            'published_at' => $status === Agenda::STATUS_PUBLISHED ? now() : null,
            'thumbnail' => $this->storeThumbnail($request),
            'organizer' => $validated['organizer'] ?? 'Kecamatan Sindang Kelingi',
        ]);

        return redirect()
            ->route('admin.agenda.index')
            ->with('success', 'Agenda kegiatan berhasil ditambahkan.');
    }

    public function edit(Agenda $agenda): View
    {
        return view('admin.agenda.edit', compact('agenda'));
    }

    public function update(Request $request, Agenda $agenda): RedirectResponse
    {
        $validated = $this->validateAgenda($request);
        $status = $request->input('status', Agenda::STATUS_DRAFT);

        $slug = $agenda->slug;
        if ($agenda->title !== $validated['title']) {
            $slug = Agenda::uniqueSlug($validated['title'], $agenda->id);
        }

        $data = [
            ...$validated,
            'slug' => $slug,
            'status' => $status,
            'organizer' => $validated['organizer'] ?? 'Kecamatan Sindang Kelingi',
            'published_at' => $status === Agenda::STATUS_PUBLISHED
                ? ($agenda->published_at ?? now())
                : null,
        ];

        if ($request->boolean('remove_thumbnail') && $agenda->thumbnail) {
            Storage::disk('public')->delete($agenda->thumbnail);
            $data['thumbnail'] = null;
        } elseif ($request->hasFile('thumbnail')) {
            if ($agenda->thumbnail) {
                Storage::disk('public')->delete($agenda->thumbnail);
            }
            $data['thumbnail'] = $this->storeThumbnail($request);
        }

        $agenda->update($data);

        return redirect()
            ->route('admin.agenda.index')
            ->with('success', 'Agenda kegiatan berhasil diperbarui.');
    }

    public function destroy(Agenda $agenda): RedirectResponse
    {
        if ($agenda->thumbnail) {
            Storage::disk('public')->delete($agenda->thumbnail);
        }

        $agenda->delete();

        return redirect()
            ->route('admin.agenda.index')
            ->with('success', 'Agenda kegiatan berhasil dihapus.');
    }

    public function uploadEditorImage(Request $request)
    {
        $request->merge(['folder' => 'agendas/editor']);

        return $this->traitUploadEditorImage($request);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateAgenda(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_type' => ['required', Rule::in(array_keys(Agenda::EVENT_TYPES))],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'event_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'location_address' => ['nullable', 'string', 'max:1000'],
            'is_online' => ['nullable', 'boolean'],
            'online_link' => ['nullable', 'url', 'max:500'],
            'organizer' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'registration_url' => ['nullable', 'url', 'max:500'],
            'status' => ['required', Rule::in(array_keys(Agenda::STATUSES))],
            'is_featured' => ['nullable', 'boolean'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ], [], [
            'title' => 'judul kegiatan',
            'event_type' => 'jenis kegiatan',
            'excerpt' => 'ringkasan',
            'content' => 'deskripsi',
            'event_date' => 'tanggal',
            'start_time' => 'jam mulai',
            'end_time' => 'jam selesai',
            'location' => 'lokasi',
            'location_address' => 'alamat lokasi',
            'is_online' => 'kegiatan daring',
            'online_link' => 'tautan daring',
            'organizer' => 'penyelenggara',
            'contact_name' => 'nama PIC',
            'contact_phone' => 'telepon PIC',
            'registration_url' => 'tautan pendaftaran',
            'status' => 'status',
            'is_featured' => 'sorotan',
            'thumbnail' => 'gambar kegiatan',
        ]);

        $validated['is_online'] = $request->boolean('is_online');
        $validated['is_featured'] = $request->boolean('is_featured');

        return $validated;
    }

    private function storeThumbnail(Request $request): ?string
    {
        if (! $request->hasFile('thumbnail')) {
            return null;
        }

        return $request->file('thumbnail')->store('agendas/thumbnails', 'public');
    }
}
