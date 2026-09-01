<div class="kecamatan-layanan-fields mt-4 pt-4 border-top">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h5 class="mb-1">Field Formulir Dinamis</h5>
            <p class="text-muted mb-0 fs-13">Atur field yang akan diisi masyarakat di halaman publik (tanpa login).</p>
        </div>
        <span class="badge bg-soft-secondary text-secondary px-3 py-2">{{ $layanan->fields->count() }} field</span>
    </div>

    <div class="card border mb-4">
        <div class="card-body">
            <h6 class="mb-3"><i class="feather-plus-circle me-2 text-primary"></i>Tambah Field Baru</h6>
            <form method="POST" action="{{ route('admin.layanan.fields.store', $layanan) }}" id="layanan-add-field-form">
                @csrf
                @include('admin.layanan._field-form', ['field' => null, 'formId' => 'layanan-add-field-form'])
                <button type="submit" class="btn btn-primary btn-sm mt-2">
                    <i class="feather-plus me-1"></i> Tambah Field
                </button>
            </form>
        </div>
    </div>

    @if ($layanan->fields->isEmpty())
        <div class="kecamatan-layanan-fields-empty text-center py-5">
            <i class="feather-list fs-1 text-muted mb-3 d-block"></i>
            <p class="text-muted mb-0">Belum ada field. Tambahkan field formulir di atas.</p>
        </div>
    @else
        <div class="row g-3">
            @foreach ($layanan->fields as $field)
                <div class="col-12">
                    <div class="card border shadow-sm">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                                <div>
                                    <div class="fw-semibold fs-16">{{ $field->name }}</div>
                                    <div class="text-muted fs-12">
                                        <code>{{ $field->field_key }}</code>
                                        · {{ $field->typeLabel() }}
                                        @if ($field->is_required)
                                            · <span class="text-danger">Wajib</span>
                                        @endif
                                    </div>
                                </div>
                                <form action="{{ route('admin.layanan.fields.destroy', [$layanan, $field]) }}" method="POST" onsubmit="return confirm('Hapus field ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </form>
                            </div>
                            <form method="POST" action="{{ route('admin.layanan.fields.update', [$layanan, $field]) }}" id="layanan-field-form-{{ $field->id }}">
                                @csrf
                                @method('PUT')
                                @include('admin.layanan._field-form', ['field' => $field, 'formId' => 'layanan-field-form-'.$field->id])
                                <button type="submit" class="btn btn-sm btn-light-brand mt-2">Simpan Field</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function toggleOptions(select) {
        const form = select.closest('form');
        const wrap = form?.querySelector('.layanan-field-options-wrap');
        if (!wrap) return;
        const needsOptions = ['select', 'radio'].includes(select.value);
        wrap.style.display = needsOptions ? '' : 'none';
    }

    document.querySelectorAll('.layanan-field-type').forEach(function (select) {
        select.addEventListener('change', function () { toggleOptions(this); });
        toggleOptions(select);
    });
});
</script>
@endpush
