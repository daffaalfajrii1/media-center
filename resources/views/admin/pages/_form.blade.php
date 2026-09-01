@php $item = $page ?? null; @endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="fw-semibold">Judul *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $item?->title) }}" required>
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Urutan</label>
        <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $item?->sort_order ?? 0) }}">
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Grup Menu *</label>
        <select name="menu_group" class="form-select" required>
            @foreach (App\Models\Page::MENU_GROUPS as $value => $label)
                <option value="{{ $value }}" @selected(old('menu_group', $item?->menu_group ?? App\Models\Page::GROUP_PROFIL) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="fw-semibold">Ringkasan</label>
        <textarea name="excerpt" class="form-control" rows="2" maxlength="500">{{ old('excerpt', $item?->excerpt) }}</textarea>
        <div class="form-text">Tampil di preview halaman (opsional).</div>
    </div>
    <div class="col-12">
        <label class="fw-semibold">Konten</label>
        <div class="kecamatan-page-editor">
            <textarea
                id="page-content"
                name="content"
                class="form-control"
                rows="12"
            >{{ old('content', $item?->content) }}</textarea>
            <div id="page-editor-status" class="kecamatan-page-editor-status mt-2" aria-live="polite"></div>
        </div>
        <div class="form-text">Gunakan editor untuk format teks, sisipkan gambar, tabel, dan daftar.</div>
    </div>
    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="is_published" value="1" class="form-check-input" id="page-published" @checked(old('is_published', $item?->is_published ?? true))>
            <label class="form-check-label" for="page-published">Terbitkan di website publik</label>
        </div>
    </div>
</div>
