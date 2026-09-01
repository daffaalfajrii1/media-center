@extends('admin.layouts.app')

@section('title', 'Kategori Berita')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Kategori Berita',
        'crumbs' => [
            ['label' => 'Berita', 'url' => route('admin.berita.index')],
            ['label' => 'Kategori'],
        ],
        'actions' => '<a href="'.route('admin.berita-kategori.create').'" class="btn btn-primary"><i class="feather-plus me-2"></i>Tambah Kategori</a>',
    ])

    <div class="main-content">
        <div class="card kecamatan-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Nama</th>
                                <th>Slug</th>
                                <th>Jumlah Berita</th>
                                <th>Status</th>
                                <th class="text-end" style="min-width:160px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>{{ $categories->firstItem() + $loop->index }}</td>
                                    <td class="fw-semibold">{{ $category->name }}</td>
                                    <td><code class="fs-12">{{ $category->slug }}</code></td>
                                    <td>{{ $category->beritas_count }}</td>
                                    <td>
                                        <span class="badge {{ $category->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                            {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="kecamatan-table-actions justify-content-end">
                                            <a href="{{ route('admin.berita-kategori.edit', $category) }}" class="btn btn-sm btn-primary" title="Edit kategori">
                                                <i class="feather-edit-2"></i>
                                                <span class="d-none d-xl-inline ms-1">Edit</span>
                                            </a>
                                            <form action="{{ route('admin.berita-kategori.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus kategori" @disabled($category->beritas_count > 0)>
                                                    <i class="feather-trash-2"></i>
                                                    <span class="d-none d-xl-inline ms-1">Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada kategori berita.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $categories->links() }}</div>
            </div>
        </div>
    </div>
@endsection
