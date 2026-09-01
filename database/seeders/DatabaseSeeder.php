<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            WebsiteSettingSeeder::class,
            SuperAdminSeeder::class,
            PageSeeder::class,
            JabatanSeeder::class,
            BeritaCategorySeeder::class,
            AgendaSeeder::class,
            GaleriSeeder::class,
            PengumumanSeeder::class,
            LayananSeeder::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@sindangkelingi.go.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        $editor = User::firstOrCreate(
            ['email' => 'editor@sindangkelingi.go.id'],
            [
                'name' => 'Editor Konten',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $editor->syncRoles(['editor']);
    }
}
