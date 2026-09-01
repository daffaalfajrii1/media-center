@extends('admin.layouts.app')

@section('title', 'Role & Permission')

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Role & Permission',
        'crumbs' => [['label' => 'Role & Permission']],
    ])

    <div class="main-content">
        <div class="alert alert-light border mb-4 fs-13">
            <i class="feather-info me-1"></i>
            Role bersifat tetap dan tidak dapat ditambah. Anda hanya dapat mengatur hak akses modul untuk setiap role.
        </div>

        <div class="row g-4">
            @foreach ($roles as $role)
                @php
                    $isSuperAdmin = $role->name === App\Models\User::ROLE_SUPER_ADMIN;
                    $canEdit = $isSuperAdmin ? auth()->user()->hasRole(App\Models\User::ROLE_SUPER_ADMIN) : in_array($role->name, App\Support\AdminPermissions::MANAGED_ROLES, true);
                @endphp
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm kecamatan-role-card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <span class="kecamatan-role-card__icon {{ $isSuperAdmin ? 'is-super' : '' }}">
                                    <i class="feather-shield"></i>
                                </span>
                                <span class="badge bg-soft-secondary text-secondary">{{ $role->users_count }} user</span>
                            </div>
                            <h5 class="mb-1">{{ App\Support\AdminPermissions::roleLabel($role->name) }}</h5>
                            <div class="text-muted fs-12 mb-2"><code>{{ $role->name }}</code></div>
                            <p class="text-muted fs-13 mb-3">{{ App\Support\AdminPermissions::ROLE_DESCRIPTIONS[$role->name] ?? '—' }}</p>
                            <div class="mb-3">
                                <span class="badge bg-soft-primary text-primary">{{ $role->permissions_count }} hak akses</span>
                            </div>
                            @if ($canEdit)
                                <a href="{{ route('admin.roles.edit', ['role' => $role->name]) }}" class="btn btn-sm btn-primary w-100">
                                    <i class="feather-edit-2 me-1"></i> Atur Hak Akses
                                </a>
                            @else
                                <button type="button" class="btn btn-sm btn-light w-100" disabled>Akses Penuh</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
