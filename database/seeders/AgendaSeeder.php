<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\User;
use Illuminate\Database\Seeder;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()->where('email', 'admin@sindangkelingi.go.id')->first()
            ?? User::query()->first();

        if (! $author) {
            return;
        }

        $samples = [
            [
                'title' => 'Rapat Koordinasi Camat dengan Kepala Desa',
                'event_type' => Agenda::TYPE_RAPAT,
                'excerpt' => 'Koordinasi program pembangunan dan evaluasi kinerja desa se-Kecamatan Sindang Kelingi.',
                'content' => '<p>Rapat koordinasi membahas capaian program triwulan dan rencana kegiatan selanjutnya.</p>',
                'event_date' => now()->addDays(7)->toDateString(),
                'start_time' => '09:00',
                'end_time' => '12:00',
                'location' => 'Aula Kecamatan Sindang Kelingi',
                'location_address' => 'Jl. Poros Sindang Kelingi, Kec. Sindang Kelingi',
                'organizer' => 'Kecamatan Sindang Kelingi',
                'contact_name' => 'Bagian Tata Pemerintahan',
                'contact_phone' => '081234567890',
                'status' => Agenda::STATUS_PUBLISHED,
                'is_featured' => true,
            ],
            [
                'title' => 'Sosialisasi Program Bantuan UMKM',
                'event_type' => Agenda::TYPE_SOSIALISASI,
                'excerpt' => 'Informasi skema bantuan dan pendampingan UMKM bagi pelaku usaha kecamatan.',
                'content' => '<p>Kegiatan terbuka untuk pelaku UMKM yang ingin mengikuti program pendampingan.</p>',
                'event_date' => now()->addDays(14)->toDateString(),
                'start_time' => '13:30',
                'end_time' => '15:30',
                'location' => 'Balai Desa Sindang Kelingi',
                'organizer' => 'Kecamatan Sindang Kelingi',
                'status' => Agenda::STATUS_PUBLISHED,
                'is_featured' => false,
            ],
            [
                'title' => 'Pelatihan Administrasi Desa Berbasis Digital',
                'event_type' => Agenda::TYPE_PELATIHAN,
                'excerpt' => 'Pelatihan pengelolaan dokumen dan layanan administrasi desa secara digital.',
                'event_date' => now()->addDays(21)->toDateString(),
                'start_time' => '08:00',
                'end_time' => '16:00',
                'is_online' => true,
                'online_link' => 'https://meet.google.com/contoh-link',
                'organizer' => 'Kecamatan Sindang Kelingi',
                'registration_url' => 'https://forms.google.com/contoh-pendaftaran',
                'status' => Agenda::STATUS_DRAFT,
                'is_featured' => false,
            ],
        ];

        foreach ($samples as $sample) {
            $slug = Agenda::uniqueSlug($sample['title']);

            Agenda::firstOrCreate(
                ['slug' => $slug],
                [
                    ...$sample,
                    'slug' => $slug,
                    'user_id' => $author->id,
                    'published_at' => ($sample['status'] ?? Agenda::STATUS_DRAFT) === Agenda::STATUS_PUBLISHED ? now() : null,
                ]
            );
        }
    }
}
