@php $item = $layanan ?? null; @endphp

<div class="row g-3">
    @if ($item)
        <div class="col-md-3">
            <label class="fw-semibold">Kode Layanan</label>
            <input type="text" class="form-control bg-light" value="{{ $item->code }}" readonly>
            <div class="form-text">Dibuat otomatis oleh sistem.</div>
        </div>
        <div class="col-md-6">
            <label class="fw-semibold">Nama Layanan *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $item->title) }}" required placeholder="Pembuatan Surat Keterangan Domisili">
        </div>
        <div class="col-md-3">
            <label class="fw-semibold">Urutan</label>
            <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $item->sort_order ?? 0) }}">
            <div class="form-check mt-3">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="layanan-active" @checked(old('is_active', $item->is_active ?? true))>
                <label class="form-check-label" for="layanan-active">Aktif (tampil publik)</label>
            </div>
        </div>
    @else
        <div class="col-md-9">
            <label class="fw-semibold">Nama Layanan *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Pembuatan Surat Keterangan Domisili">
        </div>
        <div class="col-md-3">
            <label class="fw-semibold">Urutan</label>
            <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', 0) }}">
            <div class="form-check mt-3">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="layanan-active" @checked(old('is_active', true))>
                <label class="form-check-label" for="layanan-active">Aktif (tampil publik)</label>
            </div>
        </div>
        <div class="col-12">
            <div class="alert alert-light border mb-0 py-2 px-3 fs-13">
                <i class="feather-info me-1"></i>
                <strong>Kode layanan</strong> akan dibuat otomatis setelah disimpan (mis. <code>LYN0001</code>).
            </div>
        </div>
    @endif
    <div class="col-12">
        <label class="fw-semibold">Deskripsi</label>
        <textarea name="description" class="form-control" rows="4" maxlength="5000" placeholder="Penjelasan layanan untuk masyarakat...">{{ old('description', $item?->description) }}</textarea>
    </div>
    @if ($item)
        <div class="col-12">
            <div class="alert alert-light border mb-0 py-2 px-3 fs-13">
                <strong>Format nomor registrasi:</strong>
                <code>{{ $item->registrationPrefix() }}-{{ now()->year }}-XXXXXX</code>
                · Slug publik: <code>{{ $item->slug }}</code>
            </div>
        </div>
    @endif
</div>
