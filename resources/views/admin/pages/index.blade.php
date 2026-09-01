@extends('admin.layouts.app')

@section('title', 'Halaman Profil')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Halaman Profil',
        'crumbs' => [['label' => 'Halaman Profil']],
        'actions' => '<a href="'.route('admin.halaman.create').'" class="btn btn-primary"><i class="feather-plus me-2"></i>Tambah Halaman</a>',
    ])

    <div class="main-content">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label mb-1">Cari judul</label>
                        <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari halaman...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Status</label>
                        <select name="is_published" class="form-select">
                            <option value="">Semua</option>
                            <option value="1" @selected(request('is_published') === '1')>Terbit</option>
                            <option value="0" @selected(request('is_published') === '0')>Draft</option>
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
                                <th>Grup Menu</th>
                                <th>Urutan</th>
                                <th>Status</th>
                                <th class="text-end" style="min-width:200px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $page)
                                <tr>
                                    <td>{{ $pages->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $page->title }}</div>
                                        <div class="text-muted fs-12">/profil/{{ $page->slug }}</div>
                                    </td>
                                    <td>{{ App\Models\Page::MENU_GROUPS[$page->menu_group] ?? $page->menu_group }}</td>
                                    <td>{{ $page->sort_order }}</td>
                                    <td>
                                        <span class="badge {{ $page->is_published ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }}">
                                            {{ $page->is_published ? 'Terbit' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="kecamatan-table-actions justify-content-end">
                                            @if ($page->is_published)
                                                <a href="{{ route('public.profil.show', $page->slug) }}" target="_blank" class="btn btn-sm btn-light-brand" title="Lihat di website">
                                                    <i class="feather-external-link"></i>
                                                    <span class="d-none d-xl-inline ms-1">Lihat</span>
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.halaman.edit', $page) }}" class="btn btn-sm btn-primary" title="Edit halaman">
                                                <i class="feather-edit-2"></i>
                                                <span class="d-none d-xl-inline ms-1">Edit</span>
                                            </a>
                                            <form action="{{ route('admin.halaman.destroy', $page) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus halaman ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus halaman">
                                                    <i class="feather-trash-2"></i>
                                                    <span class="d-none d-xl-inline ms-1">Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada halaman profil.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $pages->links() }}</div>
            </div>
        </div>
    </div>
@endsection
