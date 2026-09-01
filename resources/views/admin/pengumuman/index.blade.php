@extends('admin.layouts.app')

@section('title', 'Pengumuman')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Pengumuman',
        'crumbs' => [['label' => 'Pengumuman']],
        'actions' => '<a href="'.route('admin.pengumuman.create').'" class="btn btn-primary"><i class="feather-plus me-2"></i>Tambah Pengumuman</a>',
    ])

    <div class="main-content">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label mb-1">Cari judul</label>
                        <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari pengumuman...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Prioritas</label>
                        <select name="priority" class="form-select">
                            <option value="">Semua</option>
                            @foreach (App\Models\Pengumuman::PRIORITIES as $value => $label)
                                <option value="{{ $value }}" @selected(request('priority') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            @foreach (App\Models\Pengumuman::STATUSES as $value => $label)
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
                                <th>Judul</th>
                                <th>Prioritas</th>
                                <th>Lampiran</th>
                                <th>Berlaku</th>
                                <th>Status</th>
                                <th class="text-end" style="min-width:160px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengumumans as $pengumuman)
                                <tr>
                                    <td>{{ $pengumumans->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $pengumuman->title }}
                                            @if ($pengumuman->is_featured)
                                                <span class="badge bg-soft-warning text-warning ms-1">Sorotan</span>
                                            @endif
                                        </div>
                                        <div class="text-muted fs-12">{{ $pengumuman->slug }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $pengumuman->isPenting() ? 'bg-soft-danger text-danger' : 'bg-soft-secondary text-secondary' }}">
                                            {{ $pengumuman->priorityLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($pengumuman->attachments_count > 0)
                                            <span class="badge bg-soft-warning text-warning">
                                                <i class="feather-paperclip me-1"></i>{{ $pengumuman->attachments_count }} PDF
                                            </span>
                                        @else
                                            <span class="text-muted fs-12">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($pengumuman->valid_until)
                                            <span class="{{ $pengumuman->isExpired() ? 'text-danger' : '' }}">
                                                {{ $pengumuman->valid_until->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted fs-12">Tidak terbatas</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $pengumuman->isPublished() ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }}">
                                            {{ $pengumuman->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="kecamatan-table-actions justify-content-end">
                                            <a href="{{ route('admin.pengumuman.edit', $pengumuman) }}" class="btn btn-sm btn-primary" title="Edit pengumuman">
                                                <i class="feather-edit-2"></i>
                                                <span class="d-none d-xl-inline ms-1">Edit</span>
                                            </a>
                                            <form action="{{ route('admin.pengumuman.destroy', $pengumuman) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengumuman ini beserta semua PDF-nya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus pengumuman">
                                                    <i class="feather-trash-2"></i>
                                                    <span class="d-none d-xl-inline ms-1">Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada pengumuman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $pengumumans->links() }}</div>
            </div>
        </div>
    </div>
@endsection
