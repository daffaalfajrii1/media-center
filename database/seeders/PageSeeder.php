<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::where('email', 'superadmin@sindangkelingi.go.id')->value('id');

        $pages = [
            [
                'title' => 'Kata Sambutan',
                'slug' => 'kata-sambutan',
                'sort_order' => 1,
                'excerpt' => 'Sambutan Camat Kecamatan Sindang Kelingi.',
                'content' => '<p>Selamat datang di website resmi Kecamatan Sindang Kelingi. Melalui portal ini, kami berkomitmen memberikan pelayanan publik yang transparan dan informasi yang akurat bagi masyarakat.</p>',
            ],
            [
                'title' => 'Profil Kecamatan',
                'slug' => 'profil-kecamatan',
                'sort_order' => 2,
                'excerpt' => 'Gambaran umum Kecamatan Sindang Kelingi.',
                'content' => '<p>Kecamatan Sindang Kelingi merupakan salah satu kecamatan yang berkomitmen pada peningkatan pelayanan publik dan pembangunan daerah.</p>',
            ],
            [
                'title' => 'Visi dan Misi',
                'slug' => 'visi-dan-misi',
                'sort_order' => 3,
                'excerpt' => 'Visi dan misi pembangunan kecamatan.',
                'content' => '<p><strong>Visi:</strong> Kecamatan yang mandiri, sejahtera, dan berkeadilan.</p><p><strong>Misi:</strong> Meningkatkan kualitas pelayanan publik, transparansi pemerintahan, dan kesejahteraan masyarakat.</p>',
            ],
            [
                'title' => 'Program Kerja',
                'slug' => 'program-kerja',
                'sort_order' => 4,
                'excerpt' => 'Program kerja kecamatan tahun berjalan.',
                'content' => '<p>Program kerja kecamatan disusun untuk mendukung prioritas pembangunan daerah dan peningkatan kualitas hidup masyarakat.</p>',
            ],
            [
                'title' => 'Wilayah Kelurahan',
                'slug' => 'wilayah-kelurahan',
                'sort_order' => 5,
                'excerpt' => 'Informasi kelurahan di wilayah kecamatan.',
                'content' => '<p>Kecamatan Sindang Kelingi terdiri dari beberapa kelurahan yang masing-masing memiliki peran strategis dalam pembangunan dan pelayanan masyarakat.</p>',
            ],
        ];

        foreach ($pages as $index => $page) {
            Page::updateOrCreate(
                [
                    'menu_group' => Page::GROUP_PROFIL,
                    'slug' => $page['slug'],
                ],
                [
                    'title' => $page['title'],
                    'excerpt' => $page['excerpt'],
                    'content' => $page['content'],
                    'sort_order' => $page['sort_order'],
                    'is_published' => true,
                    'published_at' => now(),
                    'user_id' => $userId,
                ]
            );
        }
    }
}
