<?php

namespace Database\Seeders;

use App\Models\BeritaCategory;
use Illuminate\Database\Seeder;

class BeritaCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Umum', 'description' => 'Berita umum kecamatan'],
            ['name' => 'Pemerintahan', 'description' => 'Kegiatan dan program pemerintahan'],
            ['name' => 'Pendidikan', 'description' => 'Berita seputar pendidikan'],
            ['name' => 'Kesehatan', 'description' => 'Program dan informasi kesehatan'],
            ['name' => 'Ekonomi & UMKM', 'description' => 'Pemberdayaan ekonomi dan UMKM'],
        ];

        foreach ($categories as $category) {
            BeritaCategory::firstOrCreate(
                ['slug' => BeritaCategory::uniqueSlug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
