<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view dashboard',
            'manage users',
            'manage roles',
            'manage berita',
            'manage agenda',
            'manage galeri',
            'manage profil',
            'manage pegawai',
            'manage pengumuman',
            'manage layanan',
            'manage pengaturan',
            'manage api tokens',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $superAdmin = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $admin->syncPermissions([
            'view dashboard',
            'manage users',
            'manage berita',
            'manage agenda',
            'manage galeri',
            'manage profil',
            'manage pegawai',
            'manage pengumuman',
            'manage layanan',
            'manage api tokens',
        ]);

        $editor = Role::firstOrCreate([
            'name' => 'editor',
            'guard_name' => 'web',
        ]);
        $editor->syncPermissions([
            'view dashboard',
            'manage berita',
            'manage agenda',
            'manage galeri',
            'manage pengumuman',
        ]);

        $operator = Role::firstOrCreate([
            'name' => 'operator',
            'guard_name' => 'web',
        ]);
        $operator->syncPermissions([
            'view dashboard',
            'manage berita',
            'manage pengumuman',
        ]);
    }
}
