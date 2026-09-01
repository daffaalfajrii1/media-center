@php $item = $galeri ?? null; @endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="fw-semibold">Judul Album / Kegiatan *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $item?->title) }}" required placeholder="Contoh: Bakti Sosial Kecamatan 2026">
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Tanggal Kegiatan</label>
        <input type="date" name="event_date" class="form-control" value="{{ old('event_date', $item?->event_date?->format('Y-m-d')) }}">
    </div>
    <div class="col-12">
        <label class="fw-semibold">Deskripsi</label>
        <textarea name="description" class="form-control" rows="3" maxlength="2000" placeholder="Ceritakan singkat kegiatan ini...">{{ old('description', $item?->description) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Gambar Sampul (Thumbnail)</label>
        <input type="file" name="thumbnail" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
        <div class="form-text">Tampil di kartu daftar galeri. Jika kosong, pakai foto pertama album.</div>
        @if ($item?->thumbnail)
            <div class="mt-2">
                <img src="{{ $item->thumbnailUrl() }}" alt="Sampul" class="rounded border kecamatan-galeri-cover-preview">
                <div class="form-check mt-2">
                    <input type="checkbox" name="remove_thumbnail" value="1" class="form-check-input" id="remove-galeri-thumbnail">
                    <label class="form-check-label" for="remove-galeri-thumbnail">Hapus sampul</label>
                </div>
            </div>
        @endif
    </div>
    <div class="col-md-3">
        <label class="fw-semibold">Status *</label>
        <select name="status" class="form-select" required>
            <option value="{{ App\Models\Galeri::STATUS_DRAFT }}" @selected(old('status', $item?->status ?? App\Models\Galeri::STATUS_DRAFT) === App\Models\Galeri::STATUS_DRAFT)>Draft</option>
            <option value="{{ App\Models\Galeri::STATUS_PUBLISHED }}" @selected(old('status', $item?->status) === App\Models\Galeri::STATUS_PUBLISHED)>Terbit</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="fw-semibold">Urutan</label>
        <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $item?->sort_order ?? 0) }}">
        <div class="form-check mt-3">
            <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="galeri-featured" @checked(old('is_featured', $item?->is_featured))>
            <label class="form-check-label" for="galeri-featured">Sorotan</label>
        </div>
    </div>
</div>
