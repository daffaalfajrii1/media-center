<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

class AdminPermissions
{
    public const LABELS = [
        'view dashboard' => 'Akses dashboard admin',
        'manage users' => 'Kelola pengguna',
        'manage roles' => 'Kelola role & permission',
        'manage berita' => 'Kelola berita',
        'manage agenda' => 'Kelola agenda kegiatan',
        'manage galeri' => 'Kelola galeri foto & video',
        'manage profil' => 'Kelola halaman profil',
        'manage pegawai' => 'Kelola pegawai & jabatan',
        'manage pengumuman' => 'Kelola pengumuman',
        'manage layanan' => 'Kelola layanan publik',
        'manage pengaturan' => 'Kelola pengaturan website',
    ];

    public const ROLE_LABELS = [
        User::ROLE_SUPER_ADMIN => 'Super Admin',
        User::ROLE_ADMIN => 'Administrator',
        User::ROLE_EDITOR => 'Editor Konten',
        User::ROLE_OPERATOR => 'Operator',
    ];

    public const ROLE_DESCRIPTIONS = [
        User::ROLE_SUPER_ADMIN => 'Akses penuh ke seluruh modul dan pengaturan sistem.',
        User::ROLE_ADMIN => 'Mengelola konten, layanan, dan pengguna.',
        User::ROLE_EDITOR => 'Fokus pada modul konten website.',
        User::ROLE_OPERATOR => 'Berita dan pengumuman saja.',
    ];

    /** @var list<string> */
    public const MANAGED_ROLES = [
        User::ROLE_ADMIN,
        User::ROLE_EDITOR,
        User::ROLE_OPERATOR,
    ];

    public const ROLE_TEMPLATES = [
        User::ROLE_ADMIN => [
            'label' => 'Administrator',
            'permissions' => [
                'view dashboard',
                'manage users',
                'manage berita',
                'manage agenda',
                'manage galeri',
                'manage profil',
                'manage pegawai',
                'manage pengumuman',
                'manage layanan',
            ],
        ],
        User::ROLE_EDITOR => [
            'label' => 'Editor Konten',
            'permissions' => [
                'view dashboard',
                'manage berita',
                'manage agenda',
                'manage galeri',
                'manage pengumuman',
            ],
        ],
        User::ROLE_OPERATOR => [
            'label' => 'Operator (Berita & Pengumuman)',
            'permissions' => [
                'view dashboard',
                'manage berita',
                'manage pengumuman',
            ],
        ],
    ];

    /** @return list<string> */
    public static function superAdminOnly(): array
    {
        return ['manage roles', 'manage pengaturan'];
    }

    public static function label(string $permission): string
    {
        return self::LABELS[$permission] ?? $permission;
    }

    public static function roleLabel(string $role): string
    {
        return self::ROLE_LABELS[$role] ?? $role;
    }

    public static function sortPermissions(Collection $permissions): Collection
    {
        $order = array_flip(array_keys(self::LABELS));

        return $permissions->sortBy(fn ($permission) => $order[$permission->name] ?? 999)->values();
    }

    public static function allPermissionNames(): array
    {
        return Permission::query()->where('guard_name', 'web')->pluck('name')->all();
    }

    public static function assignableFor(User $actor): Collection
    {
        $query = Permission::query()->where('guard_name', 'web')->orderBy('name');

        if (! $actor->hasRole(User::ROLE_SUPER_ADMIN)) {
            $query->whereNotIn('name', self::superAdminOnly());
        }

        return $query->get();
    }

    /** @param  list<string>  $requested */
    public static function sanitizeForActor(User $actor, array $requested): array
    {
        $allowed = self::assignableFor($actor)->pluck('name')->all();

        return array_values(array_intersect($requested, $allowed));
    }

    public static function canManageUser(User $actor, User $target): bool
    {
        if ($actor->id === $target->id) {
            return true;
        }

        if ($target->hasRole(User::ROLE_SUPER_ADMIN) && ! $actor->hasRole(User::ROLE_SUPER_ADMIN)) {
            return false;
        }

        return true;
    }
}
