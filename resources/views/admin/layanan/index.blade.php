@extends('admin.layouts.app')

@section('title', 'Layanan Publik')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Layanan Publik',
        'crumbs' => [['label' => 'Layanan']],
        'actions' => '
            <a href="'.route('admin.layanan-pengajuan.index').'" class="btn btn-light-brand me-2">
                <i class="feather-inbox me-2"></i>Pengajuan
            </a>
            <a href="'.route('admin.layanan.create').'" class="btn btn-primary">
                <i class="feather-plus me-2"></i>Tambah Layanan
            </a>
        ',
    ])

    <div class="main-content">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label mb-1">Cari nama / kode</label>
                        <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari layanan...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="">Semua</option>
                            <option value="1" @selected(request('is_active') === '1')>Aktif</option>
                            <option value="0" @selected(request('is_active') === '0')>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-light-brand w-100">
                            <i class="feather-filter me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card kecamatan-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Layanan</th>
                                <th>Kode</th>
                                <th>Field</th>
                                <th>Pengajuan</th>
                                <th>Status</th>
                                <th class="text-end" style="min-width:160px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($layanans as $layanan)
                                <tr>
                                    <td>{{ $layanans->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $layanan->title }}</div>
                                        <div class="text-muted fs-12">{{ $layanan->slug }}</div>
                                    </td>
                                    <td><code>{{ $layanan->code }}</code></td>
                                    <td>{{ $layanan->fields_count }} field</td>
                                    <td>
                                        @if ($layanan->pengajuans_count > 0)
                                            <a href="{{ route('admin.layanan-pengajuan.index', ['layanan_id' => $layanan->id]) }}" class="badge bg-soft-primary text-primary text-decoration-none">
                                                {{ $layanan->pengajuans_count }} pengajuan
                                            </a>
                                        @else
                                            <span class="text-muted fs-12">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $layanan->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                            {{ $layanan->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="kecamatan-table-actions justify-content-end">
                                            <a href="{{ route('admin.layanan.edit', $layanan) }}" class="btn btn-sm btn-primary">
                                                <i class="feather-edit-2"></i>
                                                <span class="d-none d-xl-inline ms-1">Atur</span>
                                            </a>
                                            <form
                                                action="{{ route('admin.layanan.destroy', $layanan) }}"
                                                method="POST"
                                                class="d-inline js-delete-layanan-form"
                                                data-title="{{ e($layanan->title) }}"
                                                data-pengajuans="{{ $layanan->pengajuans_count }}"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada layanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $layanans->links() }}</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const swal = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-danger m-1',
                    cancelButton: 'btn btn-light-brand m-1',
                },
                buttonsStyling: false,
            });

            document.querySelectorAll('.js-delete-layanan-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const title = form.dataset.title || 'layanan ini';
                    const pengajuanCount = parseInt(form.dataset.pengajuans || '0', 10);
                    const hasPengajuan = pengajuanCount > 0;

                    swal.fire({
                        title: hasPengajuan ? 'Hapus Layanan & Pengajuan?' : 'Hapus Layanan?',
                        html: hasPengajuan
                            ? '<p class="mb-2">Layanan <strong>' + title + '</strong> sudah memiliki <strong>' + pengajuanCount + ' pengajuan</strong>.</p>'
                                + '<p class="mb-0 text-muted fs-13">Semua pengajuan beserta data dan file terkait akan ikut dihapus permanen. Tindakan ini tidak dapat dibatalkan.</p>'
                            : '<p class="mb-0">Anda yakin ingin menghapus layanan <strong>' + title + '</strong>?</p>',
                        icon: hasPengajuan ? 'warning' : 'question',
                        showCancelButton: true,
                        confirmButtonText: hasPengajuan ? 'Ya, hapus semua' : 'Ya, hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        focusCancel: hasPengajuan,
                    }).then(function (result) {
                        if (result.value) {
                            HTMLFormElement.prototype.submit.call(form);
                        }
                    });
                });
            });
        });
    </script>
@endpush
