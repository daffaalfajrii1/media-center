@extends('admin.layouts.app')

@section('title', 'Agenda Kegiatan')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Agenda Kegiatan',
        'crumbs' => [['label' => 'Agenda Kegiatan']],
        'actions' => '<a href="'.route('admin.agenda.create').'" class="btn btn-primary"><i class="feather-plus me-2"></i>Tambah Agenda</a>',
    ])

    <div class="main-content">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label mb-1">Cari judul</label>
                        <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari kegiatan...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Periode</label>
                        <select name="period" class="form-select">
                            <option value="">Semua</option>
                            <option value="upcoming" @selected(request('period') === 'upcoming')>Akan datang</option>
                            <option value="past" @selected(request('period') === 'past')>Sudah lewat</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Jenis</label>
                        <select name="event_type" class="form-select">
                            <option value="">Semua</option>
                            @foreach (App\Models\Agenda::EVENT_TYPES as $value => $label)
                                <option value="{{ $value }}" @selected(request('event_type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            @foreach (App\Models\Agenda::STATUSES as $value => $label)
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
                                <th>Kegiatan</th>
                                <th>Tanggal & Waktu</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th class="text-end" style="min-width:160px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agendas as $agenda)
                                <tr>
                                    <td>{{ $agendas->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $agenda->title }}
                                            @if ($agenda->is_featured)
                                                <span class="badge bg-soft-warning text-warning ms-1">Sorotan</span>
                                            @endif
                                        </div>
                                        <div class="text-muted fs-12">{{ $agenda->eventTypeLabel() }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $agenda->event_date->format('d/m/Y') }}</div>
                                        <div class="text-muted fs-12">{{ $agenda->formattedTimeRange() }}</div>
                                    </td>
                                    <td>
                                        @if ($agenda->is_online)
                                            <span class="badge bg-soft-info text-info">Daring</span>
                                        @else
                                            {{ $agenda->locationDisplay() }}
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match ($agenda->status) {
                                                App\Models\Agenda::STATUS_PUBLISHED => 'bg-soft-success text-success',
                                                App\Models\Agenda::STATUS_CANCELLED => 'bg-soft-danger text-danger',
                                                default => 'bg-soft-warning text-warning',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $agenda->statusLabel() }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="kecamatan-table-actions justify-content-end">
                                            <a href="{{ route('admin.agenda.edit', $agenda) }}" class="btn btn-sm btn-primary" title="Edit agenda">
                                                <i class="feather-edit-2"></i>
                                                <span class="d-none d-xl-inline ms-1">Edit</span>
                                            </a>
                                            <form action="{{ route('admin.agenda.destroy', $agenda) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus agenda ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus agenda">
                                                    <i class="feather-trash-2"></i>
                                                    <span class="d-none d-xl-inline ms-1">Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada agenda kegiatan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $agendas->links() }}</div>
            </div>
        </div>
    </div>
@endsection
