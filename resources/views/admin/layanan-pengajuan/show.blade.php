@extends('admin.layouts.app')

@section('title', 'Detail Pengajuan')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Detail Pengajuan',
        'crumbs' => [
            ['label' => 'Layanan', 'url' => route('admin.layanan.index')],
            ['label' => 'Pengajuan', 'url' => route('admin.layanan-pengajuan.index')],
            ['label' => 'Detail'],
        ],
    ])

    <div class="main-content">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0">Data Pengajuan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 p-3 bg-light rounded">
                            <div class="fs-12 text-muted">Nomor Registrasi</div>
                            <div class="fs-20 fw-bold"><code>{{ $pengajuan->registration_number }}</code></div>
                            <div class="text-muted fs-13 mt-1">
                                Layanan: <strong>{{ $pengajuan->layanan?->title }}</strong> ({{ $pengajuan->layanan?->code }})
                            </div>
                            <div class="text-muted fs-13">
                                Diajukan: {{ $pengajuan->submitted_at?->format('d F Y H:i') }} WIB
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    @forelse ($pengajuan->layanan?->fields ?? [] as $field)
                                        <tr>
                                            <th style="width:35%" class="bg-light">{{ $field->name }}</th>
                                            <td>
                                                @if ($field->isFileType() && $pengajuan->fileUrl($field->field_key))
                                                    <a href="{{ $pengajuan->fileUrl($field->field_key) }}" target="_blank" class="btn btn-sm btn-light-brand">
                                                        <i class="feather-download me-1"></i> {{ $pengajuan->displayValueForField($field) }}
                                                    </a>
                                                @else
                                                    {{ $pengajuan->displayValueForField($field) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        @foreach ($pengajuan->data ?? [] as $key => $value)
                                            <tr>
                                                <th style="width:35%" class="bg-light">{{ str_replace('_', ' ', ucfirst($key)) }}</th>
                                                <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                                            </tr>
                                        @endforeach
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if ($pengajuan->status === App\Models\LayananPengajuan::STATUS_COMPLETED)
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-transparent border-bottom">
                            <h5 class="mb-0">Hasil Penyelesaian</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="badge bg-soft-success text-success">{{ $pengajuan->completionMethodLabel() }}</span>
                            </div>
                            @if ($pengajuan->hasUploadedResult())
                                <a href="{{ $pengajuan->resultFileUrl() }}" target="_blank" class="btn btn-light-brand">
                                    <i class="feather-download me-1"></i> Unduh File Hasil
                                </a>
                            @endif
                            @if ($pengajuan->completion_message)
                                <div class="mt-3 p-3 bg-light rounded fs-13">
                                    <strong>Pesan untuk pemohon:</strong><br>
                                    {{ $pengajuan->completion_message }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0">Kelola Status</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $statusBadge = match ($pengajuan->status) {
                                App\Models\LayananPengajuan::STATUS_COMPLETED => 'bg-soft-success text-success',
                                App\Models\LayananPengajuan::STATUS_PROCESSING => 'bg-soft-primary text-primary',
                                App\Models\LayananPengajuan::STATUS_REJECTED => 'bg-soft-danger text-danger',
                                default => 'bg-soft-warning text-warning',
                            };
                        @endphp
                        <div class="mb-3">
                            <span class="badge {{ $statusBadge }} px-3 py-2">{{ $pengajuan->statusLabel() }}</span>
                        </div>

                        @if ($pengajuan->processed_at)
                            <div class="fs-13 text-muted mb-3">
                                Terakhir diproses: {{ $pengajuan->processed_at->format('d/m/Y H:i') }}
                                @if ($pengajuan->processor)
                                    oleh {{ $pengajuan->processor->name }}
                                @endif
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.layanan-pengajuan.status', $pengajuan) }}" enctype="multipart/form-data" id="pengajuan-status-form">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="fw-semibold">Status *</label>
                                <select name="status" class="form-select" id="pengajuan-status" required>
                                    @foreach (App\Models\LayananPengajuan::STATUSES as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $pengajuan->status) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="completion-section" class="border rounded p-3 mb-3 bg-light" style="display:none">
                                <h6 class="mb-3">Penyelesaian Layanan</h6>
                                <div class="mb-3">
                                    <label class="fw-semibold">Cara penyelesaian *</label>
                                    @foreach (App\Models\LayananPengajuan::COMPLETION_METHODS as $value => $label)
                                        <div class="form-check">
                                            <input type="radio" name="completion_method" value="{{ $value }}" class="form-check-input completion-method-radio" id="completion-{{ $value }}"
                                                @checked(old('completion_method', $pengajuan->completion_method) === $value)>
                                            <label class="form-check-label" for="completion-{{ $value }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="completion-upload-wrap" style="display:none">
                                    <label class="fw-semibold">Upload File Hasil</label>
                                    <input type="file" name="result_file" class="form-control" accept=".pdf,image/jpeg,image/png">
                                    <div class="form-text">PDF atau gambar. Maks. 10 MB.</div>
                                    @if ($pengajuan->result_file_path)
                                        <div class="mt-2 fs-13">
                                            File saat ini:
                                            <a href="{{ $pengajuan->resultFileUrl() }}" target="_blank">Lihat hasil</a>
                                        </div>
                                    @endif
                                </div>
                                <div id="completion-pickup-wrap" class="mt-3" style="display:none">
                                    <label class="fw-semibold">Instruksi Pengambilan di Kantor *</label>
                                    <textarea name="completion_message" class="form-control" rows="3" placeholder="Contoh: Silakan datang ke loket pelayanan Kecamatan Sindang Kelingi, Senin–Jumat 08.00–15.00 WIB, bawa KTP asli.">{{ old('completion_message', $pengajuan->completion_message) }}</textarea>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-semibold">Catatan Admin</label>
                                <textarea name="admin_note" class="form-control" rows="3" placeholder="Catatan internal atau alasan penolakan...">{{ old('admin_note', $pengajuan->admin_note) }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Simpan Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.getElementById('pengajuan-status');
    const completionSection = document.getElementById('completion-section');
    const uploadWrap = document.getElementById('completion-upload-wrap');
    const pickupWrap = document.getElementById('completion-pickup-wrap');
    const methodRadios = document.querySelectorAll('.completion-method-radio');
    const completedStatus = @json(App\Models\LayananPengajuan::STATUS_COMPLETED);
    const fileUpload = @json(App\Models\LayananPengajuan::COMPLETION_FILE_UPLOAD);
    const officePickup = @json(App\Models\LayananPengajuan::COMPLETION_OFFICE_PICKUP);

    function toggleCompletionMethod() {
        const selected = document.querySelector('.completion-method-radio:checked');
        const method = selected?.value;
        if (uploadWrap) uploadWrap.style.display = method === fileUpload ? '' : 'none';
        if (pickupWrap) pickupWrap.style.display = method === officePickup ? '' : 'none';
    }

    function toggleCompletionSection() {
        const show = statusSelect?.value === completedStatus;
        if (completionSection) completionSection.style.display = show ? '' : 'none';
        if (show) toggleCompletionMethod();
    }

    statusSelect?.addEventListener('change', toggleCompletionSection);
    methodRadios.forEach(r => r.addEventListener('change', toggleCompletionMethod));
    toggleCompletionSection();
});
</script>
@endpush
