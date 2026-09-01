@php
    $inputName = 'fields['.$field->field_key.']';
    $errorKey = 'fields.'.$field->field_key;
    $inputId = 'field_'.$field->field_key;
    $oldValue = old('fields.'.$field->field_key);
@endphp

<div class="field-set mb-4">
    <label for="{{ $inputId }}" class="form-label fw-600">
        {{ $field->name }}
        @if ($field->is_required)
            <span class="text-danger">*</span>
        @endif
    </label>

    @switch($field->type)
        @case(\App\Models\LayananField::TYPE_TEXTAREA)
            <textarea
                name="{{ $inputName }}"
                id="{{ $inputId }}"
                class="form-control @error($errorKey) is-invalid @enderror"
                rows="4"
                placeholder="{{ $field->placeholder }}"
                @if ($field->is_required) required @endif
            >{{ $oldValue }}</textarea>
            @break

        @case(\App\Models\LayananField::TYPE_SELECT)
            <select
                name="{{ $inputName }}"
                id="{{ $inputId }}"
                class="form-control @error($errorKey) is-invalid @enderror"
                @if ($field->is_required) required @endif
            >
                <option value="">— Pilih —</option>
                @foreach ($field->options ?? [] as $option)
                    <option value="{{ $option }}" @selected($oldValue === $option)>{{ $option }}</option>
                @endforeach
            </select>
            @break

        @case(\App\Models\LayananField::TYPE_RADIO)
            <div>
                @foreach ($field->options ?? [] as $option)
                    <div class="form-check">
                        <input
                            class="form-check-input @error($errorKey) is-invalid @enderror"
                            type="radio"
                            name="{{ $inputName }}"
                            id="{{ $inputId }}_{{ $loop->index }}"
                            value="{{ $option }}"
                            @checked($oldValue === $option)
                            @if ($field->is_required) required @endif
                        >
                        <label class="form-check-label" for="{{ $inputId }}_{{ $loop->index }}">{{ $option }}</label>
                    </div>
                @endforeach
            </div>
            @break

        @case(\App\Models\LayananField::TYPE_CHECKBOX)
            <div class="form-check">
                <input type="hidden" name="{{ $inputName }}" value="0">
                <input
                    class="form-check-input @error($errorKey) is-invalid @enderror"
                    type="checkbox"
                    name="{{ $inputName }}"
                    id="{{ $inputId }}"
                    value="1"
                    @checked((string) $oldValue === '1')
                >
                <label class="form-check-label" for="{{ $inputId }}">{{ $field->placeholder ?: 'Ya' }}</label>
            </div>
            @break

        @case(\App\Models\LayananField::TYPE_PDF)
            <input
                type="file"
                name="{{ $inputName }}"
                id="{{ $inputId }}"
                class="form-control @error($errorKey) is-invalid @enderror"
                accept=".pdf,application/pdf"
                @if ($field->is_required) required @endif
            >
            @break

        @case(\App\Models\LayananField::TYPE_IMAGE)
            <input
                type="file"
                name="{{ $inputName }}"
                id="{{ $inputId }}"
                class="form-control @error($errorKey) is-invalid @enderror"
                accept="image/jpeg,image/png,image/jpg"
                @if ($field->is_required) required @endif
            >
            @break

        @case(\App\Models\LayananField::TYPE_FILE)
            <input
                type="file"
                name="{{ $inputName }}"
                id="{{ $inputId }}"
                class="form-control @error($errorKey) is-invalid @enderror"
                @if ($field->is_required) required @endif
            >
            @break

        @default
            <input
                type="{{ $field->type === \App\Models\LayananField::TYPE_NUMBER ? 'number' : ($field->type === \App\Models\LayananField::TYPE_DATE ? 'date' : $field->type) }}"
                name="{{ $inputName }}"
                id="{{ $inputId }}"
                class="form-control @error($errorKey) is-invalid @enderror"
                value="{{ $oldValue }}"
                placeholder="{{ $field->placeholder }}"
                @if ($field->is_required) required @endif
            >
    @endswitch

    @if ($field->help_text)
        <small class="text-muted d-block mt-1">{{ $field->help_text }}</small>
    @endif

    @error($errorKey)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
