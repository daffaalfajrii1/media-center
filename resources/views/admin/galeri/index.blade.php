@extends('admin.layouts.app')

@section('title', 'Galeri Foto & Video')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Galeri Foto & Video',
        'crumbs' => [['label' => 'Galeri']],
        'actions' => '<a href="'.route('admin.galeri.create').'" class="btn btn-primary"><i class="feather-plus me-2"></i>Tambah Album</a>',
    ])

    <div class="main-content">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label mb-1">Cari album</label>
                        <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari judul kegiatan...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                            <option value="published" @selected(request('status') === 'published')>Terbit</option>
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

        @if ($galeris->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="feather-image fs-1 text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-3">Belum ada album galeri.</p>
                    <a href="{{ route('admin.galeri.create') }}" class="btn btn-primary">Buat Album Pertama</a>
                </div>
            </div>
        @else
            <div class="row g-4 kecamatan-galeri-album-grid">
                @foreach ($galeris as $galeri)
                    <div class="col-md-6 col-xl-4">
                        <div class="kecamatan-galeri-album-card card h-100 border-0 shadow-sm">
                            <div class="kecamatan-galeri-album-card__cover">
                                @if ($galeri->thumbnail)
                                    <img src="{{ asset('storage/'.$galeri->thumbnail) }}" alt="{{ $galeri->title }}" loading="lazy">
                                @else
                                    <div class="kecamatan-galeri-album-card__placeholder">
                                        <i class="feather-image"></i>
                                    </div>
                                @endif
                                @if ($galeri->is_featured)
                                    <span class="kecamatan-galeri-album-card__featured">Sorotan</span>
                                @endif
                                <span class="kecamatan-galeri-album-card__status badge {{ $galeri->isPublished() ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }}">
                                    {{ $galeri->isPublished() ? 'Terbit' : 'Draft' }}
                                </span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title mb-1 text-truncate" title="{{ $galeri->title }}">{{ $galeri->title }}</h5>
                                @if ($galeri->event_date)
                                    <div class="text-muted fs-12 mb-2">
                                        <i class="feather-calendar me-1"></i>{{ $galeri->event_date->format('d F Y') }}
                                    </div>
                                @endif
                                @if ($galeri->description)
                                    <p class="card-text text-muted fs-13 mb-2 kecamatan-galeri-album-card__desc">{{ $galeri->description }}</p>
                                @endif
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @if ($galeri->photos_count > 0)
                                        <span class="badge bg-soft-primary text-primary">
                                            <i class="feather-image me-1"></i>{{ $galeri->photos_count }} foto
                                        </span>
                                    @endif
                                    @if ($galeri->videos_count > 0)
                                        <span class="badge bg-soft-danger text-danger">
                                            <i class="feather-youtube me-1"></i>{{ $galeri->videos_count }} video
                                        </span>
                                    @endif
                                    @if ($galeri->photos_count === 0 && $galeri->videos_count === 0)
                                        <span class="badge bg-soft-secondary text-secondary">Belum ada media</span>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.galeri.edit', $galeri) }}" class="btn btn-sm btn-primary flex-grow-1">
                                        <i class="feather-edit-2 me-1"></i> Kelola
                                    </a>
                                    <form action="{{ route('admin.galeri.destroy', $galeri) }}" method="POST" onsubmit="return confirm('Hapus album ini beserta semua fotonya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus album">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $galeris->links() }}</div>
        @endif
    </div>
@endsection
