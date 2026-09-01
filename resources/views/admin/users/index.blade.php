@extends('admin.layouts.app')

@section('title', 'Pengguna')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Pengguna',
        'crumbs' => [['label' => 'Pengguna']],
        'actions' => '<a href="'.route('admin.pengguna.create').'" class="btn btn-primary"><i class="feather-user-plus me-2"></i>Tambah Pengguna</a>',
    ])

    <div class="main-content">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label mb-1">Cari nama / email</label>
                        <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari pengguna...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Role</label>
                        <select name="role" class="form-select">
                            <option value="">Semua</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ $role->name }}</option>
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
                                <th>Pengguna</th>
                                <th>Role</th>
                                <th>Hak Akses</th>
                                <th>Bergabung</th>
                                <th class="text-end" style="min-width:160px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <div class="text-muted fs-12">{{ $user->email }}</div>
                                    </td>
                                    <td>
                                        @forelse($user->roles as $role)
                                            <span class="badge bg-soft-primary text-primary">{{ $role->name }}</span>
                                        @empty
                                            <span class="badge bg-soft-secondary text-secondary">Kustom</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <span class="text-muted fs-12">{{ $user->getAllPermissions()->count() }} permission</span>
                                    </td>
                                    <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                                    <td class="text-end">
                                        <div class="kecamatan-table-actions justify-content-end">
                                            <a href="{{ route('admin.pengguna.edit', $user) }}" class="btn btn-sm btn-primary">
                                                <i class="feather-edit-2"></i>
                                                <span class="d-none d-xl-inline ms-1">Edit</span>
                                            </a>
                                            @if ($user->id !== auth()->id())
                                                <form action="{{ route('admin.pengguna.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengguna ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada pengguna.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
@endsection
