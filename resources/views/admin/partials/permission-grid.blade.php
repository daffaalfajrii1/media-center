@php
    $selected = $selectedPermissions ?? [];
    $inputName = $inputName ?? 'permissions[]';
    $checkboxClass = $checkboxClass ?? 'permission-checkbox';
    $permissionIcons = [
        'view dashboard' => 'feather-airplay',
        'manage users' => 'feather-users',
        'manage roles' => 'feather-shield',
        'manage berita' => 'feather-file-text',
        'manage agenda' => 'feather-calendar',
        'manage galeri' => 'feather-image',
        'manage profil' => 'feather-file',
        'manage pegawai' => 'feather-users',
        'manage pengumuman' => 'feather-bell',
        'manage layanan' => 'feather-layers',
        'manage pengaturan' => 'feather-settings',
    ];
    $sorted = App\Support\AdminPermissions::sortPermissions($permissions);
@endphp

<div class="kecamatan-permission-panel">
    <div class="kecamatan-permission-panel__required mb-3">
        <i class="feather-check-circle text-success me-2"></i>
        <span><strong>Akses dashboard admin</strong> — wajib untuk semua pengguna panel</span>
        <input type="hidden" name="{{ $inputName }}" value="view dashboard">
    </div>

    <div class="row g-2">
        @foreach ($sorted as $permission)
            @if ($permission->name === 'view dashboard')
                @continue
            @endif
            @php
                $checked = in_array($permission->name, old('permissions', $selected), true);
                $icon = $permissionIcons[$permission->name] ?? 'feather-check-square';
                $label = $permissionLabels[$permission->name] ?? $permission->name;
            @endphp
            <div class="col-sm-6 col-lg-4">
                <label class="kecamatan-permission-item {{ $checked ? 'is-checked' : '' }}">
                    <input
                        type="checkbox"
                        name="{{ $inputName }}"
                        value="{{ $permission->name }}"
                        class="kecamatan-permission-item__input {{ $checkboxClass }}"
                        @checked($checked)
                        @disabled(($readonly ?? false) || ($permission->name === 'view dashboard'))
                    >
                    <span class="kecamatan-permission-item__icon"><i class="{{ $icon }}"></i></span>
                    <span class="kecamatan-permission-item__label">{{ $label }}</span>
                </label>
            </div>
        @endforeach
    </div>
</div>

@once
    @push('scripts')
    <script>
    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('kecamatan-permission-item__input')) return;
        const item = e.target.closest('.kecamatan-permission-item');
        if (item) item.classList.toggle('is-checked', e.target.checked);
    });
    </script>
    @endpush
@endonce
