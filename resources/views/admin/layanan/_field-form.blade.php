@php
    $optionsText = old('options_text', $field?->options ? implode("\n", $field->options) : '');
@endphp

<div class="row g-2">
    <div class="col-md-4">
        <label class="form-label mb-1">Nama Field *</label>
        <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name', $field?->name) }}" required placeholder="Nama Lengkap">
    </div>
    <div class="col-md-3">
        <label class="form-label mb-1">Kunci Field</label>
        <input type="text" name="field_key" class="form-control form-control-sm" value="{{ old('field_key', $field?->field_key) }}" placeholder="nama_lengkap" pattern="[a-z0-9_]+">
        <div class="form-text">Opsional. Auto dari nama jika kosong.</div>
    </div>
    <div class="col-md-3">
        <label class="form-label mb-1">Tipe *</label>
        <select name="type" class="form-select form-select-sm layanan-field-type" required>
            @foreach (App\Models\LayananField::TYPES as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $field?->type ?? App\Models\LayananField::TYPE_TEXT) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_required" value="1" class="form-check-input" id="required-{{ $field?->id ?? 'new' }}" @checked(old('is_required', $field?->is_required))>
            <label class="form-check-label" for="required-{{ $field?->id ?? 'new' }}">Wajib</label>
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label mb-1">Placeholder</label>
        <input type="text" name="placeholder" class="form-control form-control-sm" value="{{ old('placeholder', $field?->placeholder) }}">
    </div>
    <div class="col-md-8">
        <label class="form-label mb-1">Petunjuk</label>
        <input type="text" name="help_text" class="form-control form-control-sm" value="{{ old('help_text', $field?->help_text) }}" placeholder="Contoh: Sesuai KTP">
    </div>
    <div class="col-12 layanan-field-options-wrap">
        <label class="form-label mb-1">Opsi Pilihan</label>
        <textarea name="options_text" class="form-control form-control-sm" rows="3" placeholder="Satu opsi per baris&#10;Laki-laki&#10;Perempuan">{{ $optionsText }}</textarea>
        <div class="form-text">Wajib untuk tipe Dropdown / Radio. Satu opsi per baris.</div>
    </div>
</div>
