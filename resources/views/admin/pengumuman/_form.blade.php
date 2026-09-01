@php $item = $pengumuman ?? null; @endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="fw-semibold">Judul Pengumuman *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $item?->title) }}" required placeholder="Contoh: Penerimaan CPNS Kecamatan 2026">
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Prioritas *</label>
        <select name="priority" class="form-select" required>
            @foreach (App\Models\Pengumuman::PRIORITIES as $value => $label)
                <option value="{{ $value }}" @selected(old('priority', $item?->priority ?? App\Models\Pengumuman::PRIORITY_NORMAL) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="fw-semibold">Ringkasan</label>
        <textarea name="excerpt" class="form-control" rows="2" maxlength="500">{{ old('excerpt', $item?->excerpt) }}</textarea>
        <div class="form-text">Tampil di daftar pengumuman (opsional, maks. 500 karakter).</div>
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Status *</label>
        <select name="status" class="form-select" required>
            @foreach (App\Models\Pengumuman::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $item?->status ?? App\Models\Pengumuman::STATUS_DRAFT) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Berlaku Sampai</label>
        <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', $item?->valid_until?->format('Y-m-d')) }}">
        <div class="form-text">Opsional. Kosongkan jika tidak ada batas waktu.</div>
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="pengumuman-featured" @checked(old('is_featured', $item?->is_featured))>
            <label class="form-check-label" for="pengumuman-featured">Sorotan di beranda</label>
        </div>
    </div>
    <div class="col-12">
        <label class="fw-semibold">Isi Pengumuman</label>
        @include('admin.partials.richtext-editor', [
            'uploadUrl' => route('admin.pengumuman.editor-upload'),
            'formId' => 'pengumuman-form',
            'textareaId' => 'pengumuman-content',
            'statusId' => 'pengumuman-editor-status',
            'content' => old('content', $item?->content),
        ])
        <div class="form-text">Detail pengumuman lengkap. File PDF unduhan ditambahkan di bagian bawah.</div>
    </div>
</div>
