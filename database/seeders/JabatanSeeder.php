<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            ['name' => 'Camat', 'sort_order' => 1, 'description' => 'Kepala Kecamatan Sindang Kelingi'],
            ['name' => 'Sekretaris Camat', 'sort_order' => 2, 'description' => 'Sekretaris Kecamatan'],
            ['name' => 'Kasi Pemerintahan', 'sort_order' => 3, 'description' => 'Kepala Seksi Pemerintahan'],
            ['name' => 'Kasi Pelayanan', 'sort_order' => 4, 'description' => 'Kepala Seksi Pelayanan'],
            ['name' => 'Staf', 'sort_order' => 5, 'description' => 'Staf Kecamatan'],
        ];

        foreach ($jabatans as $data) {
            Jabatan::firstOrCreate(
                ['slug' => Jabatan::uniqueSlug($data['name'])],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'sort_order' => $data['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $camat = Jabatan::where('slug', 'camat')->first();
        $sekretaris = Jabatan::where('slug', 'sekretaris-camat')->first();

        if ($camat) {
            Pegawai::firstOrCreate(
                ['name' => 'Dr. Ahmad Fauzi, M.Si', 'jabatan_id' => $camat->id],
                [
                    'nip' => '197501012010011001',
                    'email' => 'camat@sindangkelingi.go.id',
                    'phone' => '(0732) 21001',
                    'bio' => 'Camat Kecamatan Sindang Kelingi periode 2020–sekarang.',
                    'sort_order' => 1,
                    'is_published' => true,
                ]
            );
        }

        if ($sekretaris) {
            Pegawai::firstOrCreate(
                ['name' => 'Dra. Siti Rahmawati', 'jabatan_id' => $sekretaris->id],
                [
                    'nip' => '198203152012032002',
                    'email' => 'sekretaris@sindangkelingi.go.id',
                    'phone' => '(0732) 21002',
                    'bio' => 'Sekretaris Camat Kecamatan Sindang Kelingi.',
                    'sort_order' => 1,
                    'is_published' => true,
                ]
            );
        }
    }
}
