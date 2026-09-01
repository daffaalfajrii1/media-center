@php $item = $berita ?? null; @endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="fw-semibold">Judul *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $item?->title) }}" required>
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Kategori</label>
        <select name="berita_category_id" class="form-select">
            <option value="" @selected(old('berita_category_id', $item?->berita_category_id) === null)>Tanpa Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('berita_category_id', $item?->berita_category_id) == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <div class="form-text">Opsional. Kosongkan untuk menyimpan tanpa kategori.</div>
    </div>
    <div class="col-12">
        <label class="fw-semibold">Slug URL (SEO)</label>
        <div class="input-group">
            <span class="input-group-text text-muted">/berita/</span>
            <input type="text" class="form-control bg-light" id="berita-slug-preview"
                value="{{ old('slug', $item?->slug) }}"
                readonly>
        </div>
        <div class="form-text">Dibuat otomatis dari judul. URL ini dipakai di website publik dan panel admin.</div>
        @if ($item)
            <div class="mt-2">
                <a href="{{ $item->publicUrl() }}" target="_blank" rel="noopener" class="fs-12">
                    <i class="feather-external-link me-1"></i>Lihat di website
                </a>
            </div>
        @endif
    </div>
    <div class="col-12">
        <label class="fw-semibold">Ringkasan</label>
        <textarea name="excerpt" class="form-control" rows="2" maxlength="500">{{ old('excerpt', $item?->excerpt) }}</textarea>
        <div class="form-text">Tampil di daftar berita (opsional, maks. 500 karakter).</div>
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Gambar Utama</label>
        <input type="file" name="thumbnail" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
        <div class="form-text">JPG, PNG, GIF, atau WebP. Maks. 2 MB.</div>
        @if ($item?->thumbnail)
            <div class="mt-2">
                <img src="{{ $item->thumbnailUrl() }}" alt="Thumbnail" class="rounded border" style="max-height:120px">
                <div class="form-check mt-2">
                    <input type="checkbox" name="remove_thumbnail" value="1" class="form-check-input" id="remove-thumbnail">
                    <label class="form-check-label" for="remove-thumbnail">Hapus gambar utama</label>
                </div>
            </div>
        @endif
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Status *</label>
        <select name="status" class="form-select" required>
            <option value="{{ App\Models\Berita::STATUS_DRAFT }}" @selected(old('status', $item?->status ?? App\Models\Berita::STATUS_DRAFT) === App\Models\Berita::STATUS_DRAFT)>
                Draft
            </option>
            <option value="{{ App\Models\Berita::STATUS_PUBLISHED }}" @selected(old('status', $item?->status) === App\Models\Berita::STATUS_PUBLISHED)>
                Terbit (Publish)
            </option>
        </select>
        <div class="form-text">Draft tidak tampil di website publik.</div>
        @if ($item)
            <div class="mt-3 p-2 bg-light rounded">
                <div class="fs-12 text-muted">Dilihat</div>
                <div class="fw-semibold">{{ number_format($item->views_count) }} kali</div>
            </div>
        @endif
    </div>
    <div class="col-12">
        <label class="fw-semibold">Konten</label>
        @include('admin.partials.richtext-editor', [
            'uploadUrl' => route('admin.berita.editor-upload'),
            'formId' => 'berita-form',
            'textareaId' => 'berita-content',
            'statusId' => 'berita-editor-status',
            'content' => old('content', $item?->content),
        ])
        <div class="form-text">Gunakan editor untuk format teks, sisipkan gambar, tabel, dan daftar.</div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.querySelector('input[name="title"]');
    const slugPreview = document.getElementById('berita-slug-preview');
    if (titleInput && slugPreview) {
        const toSlug = (text) => text
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/[\s_]+/g, '-')
            .replace(/-+/g, '-');

        const updateSlug = () => {
            if (slugPreview.dataset.locked === '1') return;
            slugPreview.value = toSlug(titleInput.value) || '';
        };

        if (!slugPreview.value) {
            titleInput.addEventListener('input', updateSlug);
            updateSlug();
        }
    }

    const form = document.getElementById('berita-form');
    const statusSelect = document.querySelector('#berita-form [name="status"]');
    if (!form || !statusSelect) return;

    let allowSubmit = false;

    const syncEditor = function () {
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }
    };

    const submitForm = function () {
        syncEditor();
        allowSubmit = true;
        form.submit();
    };

    form.addEventListener('submit', function (event) {
        if (allowSubmit) return;

        event.preventDefault();
        syncEditor();

        if (typeof Swal === 'undefined') {
            submitForm();
            return;
        }

        const swal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-primary m-1',
                cancelButton: 'btn btn-light-brand m-1',
            },
            buttonsStyling: false,
        });

        const isPublish = statusSelect.value === '{{ App\Models\Berita::STATUS_PUBLISHED }}';

        swal.fire({
            title: isPublish ? 'Terbitkan Berita?' : 'Simpan sebagai Draft?',
            html: isPublish
                ? '<p class="mb-0">Berita akan <strong>langsung tampil</strong> di website publik setelah disimpan.</p>'
                : '<p class="mb-0">Berita akan disimpan sebagai <strong>draft</strong> dan tidak tampil di website publik.</p>',
            type: isPublish ? 'question' : 'info',
            showCancelButton: true,
            confirmButtonText: isPublish ? 'Ya, Terbitkan' : 'Ya, Simpan Draft',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then(function (result) {
            if (result.isConfirmed || result.value === true) {
                submitForm();
            }
        });
    });
});
</script>
@endpush
