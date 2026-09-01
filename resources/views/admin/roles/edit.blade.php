@extends('admin.layouts.app')

@section('title', 'Atur Role')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Atur Hak Akses Role',
        'crumbs' => [
            ['label' => 'Role & Permission', 'url' => route('admin.roles.index')],
            ['label' => App\Support\AdminPermissions::roleLabel($role->name)],
        ],
    ])

    <div class="main-content">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-4">
                    <h5 class="mb-1">{{ App\Support\AdminPermissions::roleLabel($role->name) }}</h5>
                    <div class="text-muted fs-13">
                        Kode role: <code>{{ $role->name }}</code>
                        · {{ App\Support\AdminPermissions::ROLE_DESCRIPTIONS[$role->name] ?? '' }}
                    </div>
                </div>

                @if ($isReadonly)
                    <div class="alert alert-warning fs-13">
                        Role <strong>Super Admin</strong> selalu memiliki akses penuh ke seluruh modul.
                    </div>
                    @include('admin.partials.permission-grid', [
                        'permissions' => $assignablePermissions,
                        'permissionLabels' => $permissionLabels,
                        'selectedPermissions' => $selectedPermissions,
                        'readonly' => true,
                    ])
                @else
                    <form method="POST" action="{{ route('admin.roles.update', ['role' => $role->name]) }}">
                        @csrf
                        @method('PUT')
                        <label class="fw-semibold mb-2 d-block">Hak Akses Modul</label>
                        @include('admin.partials.permission-grid', [
                            'permissions' => $assignablePermissions,
                            'permissionLabels' => $permissionLabels,
                            'selectedPermissions' => $selectedPermissions,
                        ])
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">Simpan Hak Akses</button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Batal</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
