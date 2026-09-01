@php $item = $pegawai ?? null; @endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="fw-semibold">Nama Lengkap *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $item?->name) }}" required>
        @error('name')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Jabatan *</label>
        <select name="jabatan_id" class="form-select" required>
            <option value="">Pilih jabatan</option>
            @foreach ($jabatans as $jabatan)
                <option value="{{ $jabatan->id }}" @selected(old('jabatan_id', $item?->jabatan_id) == $jabatan->id)>{{ $jabatan->name }}</option>
            @endforeach
        </select>
        @if ($jabatans->isEmpty())
            <div class="form-text text-warning">
                Belum ada jabatan.
                <a href="{{ route('admin.jabatan.create') }}">Tambah jabatan</a>
            </div>
        @endif
        @error('jabatan_id')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">NIP</label>
        <input type="text" name="nip" class="form-control" value="{{ old('nip', $item?->nip) }}">
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $item?->email) }}">
        @error('email')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="fw-semibold">Telepon</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $item?->phone) }}">
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Foto Pegawai</label>
        <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
        <div class="form-text">Disarankan foto portrait 400×500 px. Maks. 2 MB.</div>
        @if ($item?->photoUrl())
            <div class="mt-3">
                <img src="{{ $item->photoUrl() }}" alt="{{ $item->name }}" class="kecamatan-pegawai-form-preview">
                <div class="form-check mt-2">
                    <input type="checkbox" name="remove_photo" value="1" class="form-check-input" id="remove-photo">
                    <label class="form-check-label" for="remove-photo">Hapus foto</label>
                </div>
            </div>
        @endif
        @error('photo')<div class="text-danger fs-12 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Urutan Tampil</label>
        <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $item?->sort_order ?? 0) }}">
        <div class="form-check form-switch mt-4">
            <input type="checkbox" name="is_published" value="1" class="form-check-input" id="pegawai-published" @checked(old('is_published', $item?->is_published ?? true))>
            <label class="form-check-label" for="pegawai-published">Tampilkan di website publik</label>
        </div>
    </div>
    <div class="col-12">
        <label class="fw-semibold">Bio Singkat</label>
        <textarea name="bio" class="form-control" rows="3" maxlength="1000">{{ old('bio', $item?->bio) }}</textarea>
    </div>
</div>
