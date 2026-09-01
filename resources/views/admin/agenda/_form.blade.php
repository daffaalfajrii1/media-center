@php
    $item = $agenda ?? null;
    $startTime = old('start_time', $item?->start_time ? \Carbon\Carbon::parse($item->start_time)->format('H:i') : '');
    $endTime = old('end_time', $item?->end_time ? \Carbon\Carbon::parse($item->end_time)->format('H:i') : '');
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="fw-semibold">Judul Kegiatan *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $item?->title) }}" required>
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Jenis Kegiatan *</label>
        <select name="event_type" class="form-select" required>
            @foreach (App\Models\Agenda::EVENT_TYPES as $value => $label)
                <option value="{{ $value }}" @selected(old('event_type', $item?->event_type ?? App\Models\Agenda::TYPE_LAINNYA) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="fw-semibold">Tanggal *</label>
        <input type="date" name="event_date" class="form-control" value="{{ old('event_date', $item?->event_date?->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Jam Mulai *</label>
        <input type="time" name="start_time" class="form-control" value="{{ $startTime }}" required>
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Jam Selesai</label>
        <input type="time" name="end_time" class="form-control" value="{{ $endTime }}">
        <div class="form-text">Opsional. Harus setelah jam mulai.</div>
    </div>

    <div class="col-12">
        <label class="fw-semibold">Ringkasan</label>
        <textarea name="excerpt" class="form-control" rows="2" maxlength="500">{{ old('excerpt', $item?->excerpt) }}</textarea>
        <div class="form-text">Tampil di daftar agenda (opsional, maks. 500 karakter).</div>
    </div>

    <div class="col-md-6">
        <label class="fw-semibold">Penyelenggara</label>
        <input type="text" name="organizer" class="form-control" value="{{ old('organizer', $item?->organizer ?? 'Kecamatan Sindang Kelingi') }}" placeholder="Kecamatan Sindang Kelingi">
    </div>
    <div class="col-md-3">
        <label class="fw-semibold">Nama PIC</label>
        <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $item?->contact_name) }}" placeholder="Nama penanggung jawab">
    </div>
    <div class="col-md-3">
        <label class="fw-semibold">Telepon PIC</label>
        <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $item?->contact_phone) }}" placeholder="08xxxxxxxxxx">
    </div>

    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="is_online" value="1" class="form-check-input" id="agenda-is-online" @checked(old('is_online', $item?->is_online))>
            <label class="form-check-label" for="agenda-is-online">Kegiatan daring (online)</label>
        </div>
    </div>

    <div class="col-md-6" id="agenda-location-fields">
        <label class="fw-semibold">Nama Lokasi</label>
        <input type="text" name="location" class="form-control" value="{{ old('location', $item?->location) }}" placeholder="Contoh: Balai Kecamatan Sindang Kelingi">
    </div>
    <div class="col-md-6" id="agenda-online-link-field">
        <label class="fw-semibold">Tautan Daring</label>
        <input type="url" name="online_link" class="form-control" value="{{ old('online_link', $item?->online_link) }}" placeholder="https://zoom.us/...">
        <div class="form-text">Link Zoom, Google Meet, atau platform daring lainnya.</div>
    </div>
    <div class="col-12" id="agenda-address-field">
        <label class="fw-semibold">Alamat Lokasi</label>
        <textarea name="location_address" class="form-control" rows="2" maxlength="1000" placeholder="Alamat lengkap lokasi kegiatan">{{ old('location_address', $item?->location_address) }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="fw-semibold">Tautan Pendaftaran</label>
        <input type="url" name="registration_url" class="form-control" value="{{ old('registration_url', $item?->registration_url) }}" placeholder="https://forms.google.com/...">
        <div class="form-text">Opsional. Link formulir pendaftaran peserta.</div>
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Gambar Kegiatan</label>
        <input type="file" name="thumbnail" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
        <div class="form-text">JPG, PNG, GIF, atau WebP. Maks. 2 MB.</div>
        @if ($item?->thumbnail)
            <div class="mt-2">
                <img src="{{ $item->thumbnailUrl() }}" alt="Thumbnail" class="rounded border" style="max-height:120px">
                <div class="form-check mt-2">
                    <input type="checkbox" name="remove_thumbnail" value="1" class="form-check-input" id="remove-agenda-thumbnail">
                    <label class="form-check-label" for="remove-agenda-thumbnail">Hapus gambar</label>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <label class="fw-semibold">Status *</label>
        <select name="status" class="form-select" required>
            @foreach (App\Models\Agenda::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $item?->status ?? App\Models\Agenda::STATUS_DRAFT) === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <div class="form-text">Draft tidak tampil di website publik.</div>
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="agenda-featured" @checked(old('is_featured', $item?->is_featured))>
            <label class="form-check-label" for="agenda-featured">Sorotan (tampil menonjol)</label>
        </div>
    </div>

    <div class="col-12">
        <label class="fw-semibold">Deskripsi Kegiatan</label>
        @include('admin.partials.richtext-editor', [
            'uploadUrl' => route('admin.agenda.editor-upload'),
            'formId' => 'agenda-form',
            'textareaId' => 'agenda-content',
            'statusId' => 'agenda-editor-status',
            'content' => old('content', $item?->content),
        ])
        <div class="form-text">Detail kegiatan, rundown, peserta, dan informasi lainnya.</div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isOnline = document.getElementById('agenda-is-online');
    const locationFields = document.getElementById('agenda-location-fields');
    const addressField = document.getElementById('agenda-address-field');
    const onlineLinkField = document.getElementById('agenda-online-link-field');

    function toggleLocationFields() {
        const online = isOnline?.checked;
        if (locationFields) locationFields.style.display = online ? 'none' : '';
        if (addressField) addressField.style.display = online ? 'none' : '';
        if (onlineLinkField) onlineLinkField.style.display = online ? '' : 'none';
    }

    isOnline?.addEventListener('change', toggleLocationFields);
    toggleLocationFields();
});
</script>
@endpush
