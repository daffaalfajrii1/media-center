@php
    $item = $user ?? null;
    $selectedPermissions = $selectedPermissions ?? ['view dashboard'];
    $selectedRole = old('role', $selectedRole ?? 'custom');
    $isSuperAdmin = $item?->hasRole(App\Models\User::ROLE_SUPER_ADMIN);
    $canEditAccess = ! $isSuperAdmin || ($canAssignSuperAdmin ?? false);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="fw-semibold">Nama Lengkap *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $item?->name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Email *</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $item?->email) }}" required>
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Password {{ $item ? '' : '*' }}</label>
        <input type="password" name="password" class="form-control" {{ $item ? '' : 'required' }} autocomplete="new-password">
        @if ($item)
            <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
        @endif
    </div>
    <div class="col-md-6">
        <label class="fw-semibold">Konfirmasi Password {{ $item ? '' : '*' }}</label>
        <input type="password" name="password_confirmation" class="form-control" {{ $item ? '' : 'required' }} autocomplete="new-password">
    </div>

    @if ($canEditAccess)
        <div class="col-12">
            <div class="kecamatan-section-divider">
                <h6 class="mb-0">Hak Akses & Role</h6>
            </div>
        </div>
        <div class="col-md-5">
            <label class="fw-semibold">Template Role</label>
            <select name="role" id="user-role-template" class="form-select">
                <option value="custom" @selected($selectedRole === 'custom')>Kustom (atur manual)</option>
                @foreach ($roleTemplates as $roleKey => $template)
                    <option value="{{ $roleKey }}" @selected($selectedRole === $roleKey)>{{ $template['label'] }}</option>
                @endforeach
                @if ($canAssignSuperAdmin ?? false)
                    <option value="{{ App\Models\User::ROLE_SUPER_ADMIN }}" @selected($selectedRole === App\Models\User::ROLE_SUPER_ADMIN)>Super Admin (akses penuh)</option>
                @endif
            </select>
            <div class="form-text">Pilih template untuk mengisi hak akses otomatis.</div>
        </div>
        <div class="col-12">
            <label class="fw-semibold mb-2 d-block">Hak Akses Modul</label>
            @include('admin.partials.permission-grid', [
                'permissions' => $assignablePermissions,
                'permissionLabels' => $permissionLabels,
                'selectedPermissions' => $selectedPermissions,
                'checkboxClass' => 'user-permission-checkbox',
            ])
        </div>
    @else
        <div class="col-12">
            <div class="alert alert-warning mb-0">
                Akun <strong>Super Admin</strong> memiliki akses penuh. Hanya super admin lain yang dapat mengubah hak aksesnya.
            </div>
        </div>
    @endif
</div>

@if ($canEditAccess)
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('user-role-template');
        const templates = @json(collect($roleTemplates)->mapWithKeys(fn ($t, $k) => [$k => $t['permissions']]));
        const superAdminRole = @json(App\Models\User::ROLE_SUPER_ADMIN);
        const allPermissionNames = @json($assignablePermissions->pluck('name'));

        function setPermissions(list) {
            document.querySelectorAll('.user-permission-checkbox').forEach(function (cb) {
                if (cb.value === 'view dashboard') return;
                cb.checked = list.includes(cb.value);
                cb.closest('.kecamatan-permission-item')?.classList.toggle('is-checked', cb.checked);
            });
        }

        roleSelect?.addEventListener('change', function () {
            const role = this.value;
            if (role === 'custom') return;
            if (role === superAdminRole) {
                setPermissions(allPermissionNames);
                return;
            }
            if (templates[role]) setPermissions(templates[role]);
        });
    });
    </script>
    @endpush
@endif
