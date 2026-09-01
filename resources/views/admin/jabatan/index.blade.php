@extends('admin.layouts.app')

@section('title', 'Jabatan Pegawai')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Jabatan Pegawai',
        'crumbs' => [
            ['label' => 'Pegawai', 'url' => route('admin.pegawai.index')],
            ['label' => 'Jabatan'],
        ],
        'actions' => '<a href="'.route('admin.jabatan.create').'" class="btn btn-primary"><i class="feather-plus me-2"></i>Tambah Jabatan</a>',
    ])

    <div class="main-content">
        <div class="card kecamatan-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Nama Jabatan</th>
                                <th>Urutan</th>
                                <th>Jumlah Pegawai</th>
                                <th>Status</th>
                                <th class="text-end" style="min-width:160px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jabatans as $jabatan)
                                <tr>
                                    <td>{{ $jabatans->firstItem() + $loop->index }}</td>
                                    <td class="fw-semibold">{{ $jabatan->name }}</td>
                                    <td>{{ $jabatan->sort_order }}</td>
                                    <td>{{ $jabatan->pegawais_count }}</td>
                                    <td>
                                        <span class="badge {{ $jabatan->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                            {{ $jabatan->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="kecamatan-table-actions justify-content-end">
                                            <a href="{{ route('admin.jabatan.edit', $jabatan) }}" class="btn btn-sm btn-primary">
                                                <i class="feather-edit-2"></i>
                                            </a>
                                            <form action="{{ route('admin.jabatan.destroy', $jabatan) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jabatan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" @disabled($jabatan->pegawais_count > 0)>
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada jabatan. Tambahkan jabatan terlebih dahulu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $jabatans->links() }}</div>
            </div>
        </div>
    </div>
@endsection
