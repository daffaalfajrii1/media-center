<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\LayananField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LayananController extends Controller
{
    public function index(Request $request): View
    {
        $layanans = Layanan::query()
            ->with('author')
            ->withCount(['fields', 'pengajuans'])
            ->when($request->search, fn ($q, $search) => $q->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            }))
            ->when($request->filled('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('admin.layanan.index', compact('layanans'));
    }

    public function create(): View
    {
        return view('admin.layanan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateLayanan($request);

        $layanan = Layanan::create([
            ...$validated,
            'code' => Layanan::generateCode(),
            'slug' => Layanan::uniqueSlug($validated['title']),
            'user_id' => Auth::id(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.layanan.edit', $layanan)
            ->with('success', "Layanan berhasil dibuat dengan kode {$layanan->code}. Atur field formulir di bawah.");
    }

    public function edit(Layanan $layanan): View
    {
        $layanan->load('fields');

        return view('admin.layanan.edit', compact('layanan'));
    }

    public function update(Request $request, Layanan $layanan): RedirectResponse
    {
        $validated = $this->validateLayanan($request, $layanan);

        $slug = $layanan->slug;
        if ($layanan->title !== $validated['title']) {
            $slug = Layanan::uniqueSlug($validated['title'], $layanan->id);
        }

        $layanan->update([
            ...$validated,
            'slug' => $slug,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.layanan.edit', $layanan)
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Layanan $layanan): RedirectResponse
    {
        $pengajuanCount = $layanan->pengajuans()->count();

        $layanan->load('pengajuans');
        foreach ($layanan->pengajuans as $pengajuan) {
            $pengajuan->deleteStoredFiles();
        }

        $layanan->delete();

        $message = 'Layanan berhasil dihapus.';
        if ($pengajuanCount > 0) {
            $message .= " {$pengajuanCount} pengajuan terkait juga dihapus.";
        }

        return redirect()
            ->route('admin.layanan.index')
            ->with('success', $message);
    }

    public function storeField(Request $request, Layanan $layanan): RedirectResponse
    {
        $validated = $this->validateField($request, $layanan);

        $fieldKey = $request->filled('field_key')
            ? Str::slug($request->input('field_key'), '_')
            : LayananField::uniqueFieldKey($layanan->id, $validated['name']);

        $maxSort = (int) $layanan->fields()->max('sort_order');

        LayananField::create([
            'layanan_id' => $layanan->id,
            'name' => $validated['name'],
            'field_key' => $fieldKey,
            'type' => $validated['type'],
            'options' => LayananField::parseOptions($request->input('options_text')),
            'placeholder' => $validated['placeholder'] ?? null,
            'help_text' => $validated['help_text'] ?? null,
            'is_required' => $request->boolean('is_required'),
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()
            ->route('admin.layanan.edit', $layanan)
            ->with('success', 'Field formulir berhasil ditambahkan.');
    }

    public function updateField(Request $request, Layanan $layanan, LayananField $field): RedirectResponse
    {
        if ($field->layanan_id !== $layanan->id) {
            abort(404);
        }

        $validated = $this->validateField($request, $layanan, $field);

        $fieldKey = $request->filled('field_key')
            ? Str::slug($request->input('field_key'), '_')
            : LayananField::uniqueFieldKey($layanan->id, $validated['name'], $field->id);

        $field->update([
            'name' => $validated['name'],
            'field_key' => $fieldKey,
            'type' => $validated['type'],
            'options' => LayananField::parseOptions($request->input('options_text')),
            'placeholder' => $validated['placeholder'] ?? null,
            'help_text' => $validated['help_text'] ?? null,
            'is_required' => $request->boolean('is_required'),
        ]);

        return redirect()
            ->route('admin.layanan.edit', $layanan)
            ->with('success', 'Field formulir berhasil diperbarui.');
    }

    public function destroyField(Layanan $layanan, LayananField $field): RedirectResponse
    {
        if ($field->layanan_id !== $layanan->id) {
            abort(404);
        }

        $field->delete();

        return redirect()
            ->route('admin.layanan.edit', $layanan)
            ->with('success', 'Field formulir berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateLayanan(Request $request, ?Layanan $layanan = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'title' => 'nama layanan',
            'description' => 'deskripsi',
            'sort_order' => 'urutan',
            'is_active' => 'status aktif',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateField(Request $request, Layanan $layanan, ?LayananField $field = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'field_key' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('layanan_fields', 'field_key')
                    ->where('layanan_id', $layanan->id)
                    ->ignore($field?->id),
            ],
            'type' => ['required', Rule::in(array_keys(LayananField::TYPES))],
            'options_text' => ['nullable', 'string'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'help_text' => ['nullable', 'string', 'max:500'],
            'is_required' => ['nullable', 'boolean'],
        ], [
            'field_key.regex' => 'Kunci field hanya huruf kecil, angka, dan underscore.',
        ], [
            'name' => 'nama field',
            'field_key' => 'kunci field',
            'type' => 'tipe field',
            'options_text' => 'opsi pilihan',
            'placeholder' => 'placeholder',
            'help_text' => 'petunjuk',
            'is_required' => 'wajib diisi',
        ]);

        if (in_array($validated['type'], [LayananField::TYPE_SELECT, LayananField::TYPE_RADIO], true)) {
            $options = LayananField::parseOptions($request->input('options_text'));
            if (! $options) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'options_text' => 'Opsi pilihan wajib diisi untuk tipe ini (satu opsi per baris).',
                ]);
            }
        }

        return $validated;
    }
}
