<?php

namespace Database\Seeders;

use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Database\Seeder;

class PengumumanSeeder extends Seeder
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
                'title' => 'Penerimaan Pengaduan Masyarakat Bulan Ini',
                'excerpt' => 'Informasi layanan pengaduan dan prosedur penyampaian aspirasi warga.',
                'content' => '<p>Kecamatan Sindang Kelingi membuka layanan pengaduan masyarakat. Silakan unduh formulir pada lampiran PDF setelah pengumuman ini dipublikasikan.</p>',
                'priority' => Pengumuman::PRIORITY_NORMAL,
                'status' => Pengumuman::STATUS_PUBLISHED,
                'is_featured' => true,
                'valid_until' => now()->addMonth()->toDateString(),
            ],
            [
                'title' => 'Libur Pelayanan Administrasi Hari Raya',
                'excerpt' => 'Penutupan sementara pelayanan administrasi kecamatan.',
                'content' => '<p>Pelayanan administrasi kecamatan akan libur sesuai jadwal resmi. Pelayanan kembali normal setelah masa libur berakhir.</p>',
                'priority' => Pengumuman::PRIORITY_PENTING,
                'status' => Pengumuman::STATUS_PUBLISHED,
                'is_featured' => false,
                'valid_until' => now()->addWeeks(2)->toDateString(),
            ],
            [
                'title' => 'Seleksi Penerima Bantuan Program Desa',
                'excerpt' => 'Draft pengumuman seleksi penerima bantuan — lampiran PDF menyusul.',
                'content' => '<p>Pengumuman resmi seleksi penerima bantuan program desa akan dilengkapi dengan berkas PDF.</p>',
                'priority' => Pengumuman::PRIORITY_PENTING,
                'status' => Pengumuman::STATUS_DRAFT,
                'is_featured' => false,
            ],
        ];

        foreach ($samples as $sample) {
            $slug = Pengumuman::uniqueSlug($sample['title']);

            Pengumuman::firstOrCreate(
                ['slug' => $slug],
                [
                    ...$sample,
                    'slug' => $slug,
                    'user_id' => $author->id,
                    'published_at' => $sample['status'] === Pengumuman::STATUS_PUBLISHED ? now() : null,
                ]
            );
        }
    }
}
