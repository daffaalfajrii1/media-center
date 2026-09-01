<?php

namespace Database\Seeders;

use App\Models\Galeri;
use App\Models\GaleriItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class GaleriSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()->where('email', 'admin@sindangkelingi.go.id')->first()
            ?? User::query()->first();

        if (! $author) {
            return;
        }

        $albums = [
            [
                'title' => 'Bakti Sosial Kecamatan Sindang Kelingi',
                'description' => 'Dokumentasi kegiatan bakti sosial bersama masyarakat dan perangkat desa.',
                'event_date' => now()->subMonth()->toDateString(),
                'status' => Galeri::STATUS_PUBLISHED,
                'is_featured' => true,
                'youtube' => [
                    ['url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'title' => 'Cuplikan Kegiatan'],
                ],
            ],
            [
                'title' => 'Pelatihan Pelayanan Publik',
                'description' => 'Album foto pelatihan peningkatan kualitas pelayanan di kecamatan.',
                'event_date' => now()->subWeeks(2)->toDateString(),
                'status' => Galeri::STATUS_PUBLISHED,
                'is_featured' => false,
            ],
            [
                'title' => 'Kunjungan Kerja Camat ke Desa Binaan',
                'description' => 'Dokumentasi kunjungan kerja dan dialog dengan warga.',
                'event_date' => now()->subDays(5)->toDateString(),
                'status' => Galeri::STATUS_DRAFT,
                'is_featured' => false,
            ],
        ];

        foreach ($albums as $album) {
            $slug = Galeri::uniqueSlug($album['title']);

            $galeri = Galeri::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $album['title'],
                    'slug' => $slug,
                    'description' => $album['description'],
                    'event_date' => $album['event_date'],
                    'status' => $album['status'],
                    'is_featured' => $album['is_featured'],
                    'user_id' => $author->id,
                    'published_at' => $album['status'] === Galeri::STATUS_PUBLISHED ? now() : null,
                ]
            );

            foreach ($album['youtube'] ?? [] as $index => $video) {
                $videoId = \App\Support\Youtube::extractVideoId($video['url']);
                if (! $videoId) {
                    continue;
                }

                GaleriItem::firstOrCreate(
                    [
                        'galeri_id' => $galeri->id,
                        'type' => GaleriItem::TYPE_YOUTUBE,
                        'youtube_video_id' => $videoId,
                    ],
                    [
                        'title' => $video['title'] ?? null,
                        'youtube_url' => $video['url'],
                        'sort_order' => $index + 1,
                    ]
                );
            }
        }
    }
}
