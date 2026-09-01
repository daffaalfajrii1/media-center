@extends('admin.layouts.app')

@section('title', 'Berita')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Berita',
        'crumbs' => [['label' => 'Berita']],
        'actions' => '
            <a href="'.route('admin.berita.statistik').'" class="btn btn-light-brand me-2">
                <i class="feather-bar-chart-2 me-2"></i>Statistik
            </a>
            <a href="'.route('admin.berita-kategori.index').'" class="btn btn-light-brand me-2">
                <i class="feather-tag me-2"></i>Kategori
            </a>
            <a href="'.route('admin.berita.create').'" class="btn btn-primary">
                <i class="feather-plus me-2"></i>Tambah Berita
            </a>
        ',
    ])

    <div class="main-content">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label mb-1">Cari judul</label>
                        <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari berita...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Kategori</label>
                        <select name="berita_category_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('berita_category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
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

        <div class="card kecamatan-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Penulis</th>
                                <th>Dilihat</th>
                                <th>Status</th>
                                <th class="text-end" style="min-width:160px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($beritas as $berita)
                                <tr>
                                    <td>{{ $beritas->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $berita->title }}</div>
                                        <div class="text-muted fs-12">{{ $berita->slug }}</div>
                                    </td>
                                    <td>{{ $berita->categoryName() }}</td>
                                    <td>{{ $berita->author?->name ?? '—' }}</td>
                                    <td>{{ number_format($berita->views_count) }}</td>
                                    <td>
                                        <span class="badge {{ $berita->isPublished() ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }}">
                                            {{ $berita->isPublished() ? 'Terbit' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="kecamatan-table-actions justify-content-end">
                                            <a href="{{ route('admin.berita.edit', $berita) }}" class="btn btn-sm btn-primary" title="Edit berita">
                                                <i class="feather-edit-2"></i>
                                                <span class="d-none d-xl-inline ms-1">Edit</span>
                                            </a>
                                            <form action="{{ route('admin.berita.destroy', $berita) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus berita ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus berita">
                                                    <i class="feather-trash-2"></i>
                                                    <span class="d-none d-xl-inline ms-1">Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada berita.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $beritas->links() }}</div>
            </div>
        </div>
    </div>
@endsection
