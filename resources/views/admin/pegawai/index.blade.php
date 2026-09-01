@extends('admin.layouts.app')

@section('title', 'Pegawai')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Data Pegawai',
        'crumbs' => [['label' => 'Pegawai']],
        'actions' => '
            <a href="'.route('admin.jabatan.index').'" class="btn btn-light me-2"><i class="feather-briefcase me-2"></i>Jabatan</a>
            <a href="'.route('admin.pegawai.create').'" class="btn btn-primary"><i class="feather-plus me-2"></i>Tambah Pegawai</a>
        ',
    ])

    <div class="main-content">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="fw-semibold fs-13">Cari Nama</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama pegawai...">
                    </div>
                    <div class="col-md-3">
                        <label class="fw-semibold fs-13">Jabatan</label>
                        <select name="jabatan_id" class="form-select">
                            <option value="">Semua jabatan</option>
                            @foreach ($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}" @selected(request('jabatan_id') == $jabatan->id)>{{ $jabatan->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-semibold fs-13">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            <option value="published" @selected(request('status') === 'published')>Tampil publik</option>
                            <option value="draft" @selected(request('status') === 'draft')>Disembunyikan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-light-brand w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">
            @forelse($pegawais as $pegawai)
                <div class="col-md-6 col-xl-4 col-xxl-3">
                    <div class="card kecamatan-pegawai-admin-card h-100 border-0 shadow-sm">
                        <div class="kecamatan-pegawai-admin-card__photo-wrap">
                            @if ($pegawai->photoUrl())
                                <img src="{{ $pegawai->photoUrl() }}" alt="{{ $pegawai->name }}" class="kecamatan-pegawai-admin-card__photo">
                            @else
                                <div class="kecamatan-pegawai-admin-card__fallback">{{ $pegawai->initials() }}</div>
                            @endif
                            <span class="badge {{ $pegawai->is_published ? 'bg-success' : 'bg-secondary' }} kecamatan-pegawai-admin-card__badge">
                                {{ $pegawai->is_published ? 'Publik' : 'Draft' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-1 text-truncate" title="{{ $pegawai->name }}">{{ $pegawai->name }}</h6>
                            <div class="text-primary fs-13 mb-2">{{ $pegawai->jabatan?->name }}</div>
                            @if ($pegawai->nip)
                                <div class="text-muted fs-12 mb-1">NIP: {{ $pegawai->nip }}</div>
                            @endif
                            @if ($pegawai->phone)
                                <div class="text-muted fs-12"><i class="feather-phone me-1"></i>{{ $pegawai->phone }}</div>
                            @endif
                            <div class="d-flex gap-2 mt-3">
                                <a href="{{ route('admin.pegawai.edit', $pegawai) }}" class="btn btn-sm btn-primary flex-fill">
                                    <i class="feather-edit-2 me-1"></i> Edit
                                </a>
                                <form action="{{ route('admin.pegawai.destroy', $pegawai) }}" method="POST" onsubmit="return confirm('Hapus pegawai ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="feather-trash-2"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center text-muted py-5">
                            <i class="feather-users d-block mb-2" style="font-size:2.5rem;"></i>
                            Belum ada data pegawai.
                            <div class="mt-3">
                                <a href="{{ route('admin.jabatan.create') }}" class="btn btn-light me-2">Tambah Jabatan</a>
                                <a href="{{ route('admin.pegawai.create') }}" class="btn btn-primary">Tambah Pegawai</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($pegawais->hasPages())
            <div class="mt-4">{{ $pegawais->links() }}</div>
        @endif
    </div>
@endsection
