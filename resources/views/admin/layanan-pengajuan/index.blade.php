@extends('admin.layouts.app')

@section('title', 'Pengajuan Layanan')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Pengajuan Layanan',
        'crumbs' => [
            ['label' => 'Layanan', 'url' => route('admin.layanan.index')],
            ['label' => 'Pengajuan'],
        ],
    ])

    <div class="main-content">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label mb-1">No. Registrasi</label>
                        <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="DOMISILI-2026-000001">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Layanan</label>
                        <select name="layanan_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach ($layanans as $layanan)
                                <option value="{{ $layanan->id }}" @selected(request('layanan_id') == $layanan->id)>
                                    {{ $layanan->title }} ({{ $layanan->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            @foreach (App\Models\LayananPengajuan::STATUSES as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                            @endforeach
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
                                <th>No. Registrasi</th>
                                <th>Layanan</th>
                                <th>Diajukan</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuans as $pengajuan)
                                <tr>
                                    <td>{{ $pengajuans->firstItem() + $loop->index }}</td>
                                    <td><code class="fw-semibold">{{ $pengajuan->registration_number }}</code></td>
                                    <td>
                                        <div class="fw-semibold">{{ $pengajuan->layanan?->title }}</div>
                                        <div class="text-muted fs-12">{{ $pengajuan->layanan?->code }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $pengajuan->submitted_at?->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $badge = match ($pengajuan->status) {
                                                App\Models\LayananPengajuan::STATUS_COMPLETED => 'bg-soft-success text-success',
                                                App\Models\LayananPengajuan::STATUS_PROCESSING => 'bg-soft-primary text-primary',
                                                App\Models\LayananPengajuan::STATUS_REJECTED => 'bg-soft-danger text-danger',
                                                default => 'bg-soft-warning text-warning',
                                            };
                                        @endphp
                                        <span class="badge {{ $badge }}">{{ $pengajuan->statusLabel() }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.layanan-pengajuan.show', $pengajuan) }}" class="btn btn-sm btn-primary">
                                            <i class="feather-eye"></i>
                                            <span class="d-none d-xl-inline ms-1">Detail</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada pengajuan layanan. Pengajuan dari halaman publik akan muncul di sini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $pengajuans->links() }}</div>
            </div>
        </div>
    </div>
@endsection
