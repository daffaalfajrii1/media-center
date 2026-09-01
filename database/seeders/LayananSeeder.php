<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\LayananField;
use App\Models\LayananPengajuan;
use App\Models\User;
use App\Support\LayananRegistrationNumber;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()->where('email', 'admin@sindangkelingi.go.id')->first()
            ?? User::query()->first();

        if (! $author) {
            return;
        }

        $layanan = Layanan::firstOrCreate(
            ['code' => 'DOMISILI'],
            [
                'title' => 'Pembuatan Surat Keterangan Domisili',
                'slug' => Layanan::uniqueSlug('Pembuatan Surat Keterangan Domisili'),
                'description' => 'Layanan pengajuan surat keterangan domisili secara mandiri. Isi formulir dan unggah dokumen persyaratan.',
                'is_active' => true,
                'sort_order' => 1,
                'user_id' => $author->id,
            ]
        );

        $fields = [
            ['name' => 'Nama Lengkap', 'field_key' => 'nama_lengkap', 'type' => LayananField::TYPE_TEXT, 'is_required' => true, 'sort_order' => 1],
            ['name' => 'NIK', 'field_key' => 'nik', 'type' => LayananField::TYPE_TEXT, 'is_required' => true, 'placeholder' => '16 digit NIK', 'sort_order' => 2],
            ['name' => 'Alamat Domisili', 'field_key' => 'alamat', 'type' => LayananField::TYPE_TEXTAREA, 'is_required' => true, 'sort_order' => 3],
            ['name' => 'Nomor Telepon / WhatsApp', 'field_key' => 'telepon', 'type' => LayananField::TYPE_TEL, 'is_required' => true, 'sort_order' => 4],
            ['name' => 'Upload KTP', 'field_key' => 'upload_ktp', 'type' => LayananField::TYPE_PDF, 'is_required' => true, 'help_text' => 'Scan/foto KTP format PDF', 'sort_order' => 5],
            ['name' => 'Upload KK', 'field_key' => 'upload_kk', 'type' => LayananField::TYPE_PDF, 'is_required' => true, 'help_text' => 'Kartu Keluarga format PDF', 'sort_order' => 6],
        ];

        foreach ($fields as $field) {
            LayananField::firstOrCreate(
                [
                    'layanan_id' => $layanan->id,
                    'field_key' => $field['field_key'],
                ],
                [...$field, 'layanan_id' => $layanan->id]
            );
        }

        $sktm = Layanan::firstOrCreate(
            ['code' => 'SKTM'],
            [
                'title' => 'Surat Keterangan Tidak Mampu (SKTM)',
                'slug' => Layanan::uniqueSlug('Surat Keterangan Tidak Mampu SKTM'),
                'description' => 'Pengajuan SKTM untuk keperluan administrasi dan bantuan sosial.',
                'is_active' => true,
                'sort_order' => 2,
                'user_id' => $author->id,
            ]
        );

        LayananField::firstOrCreate(
            ['layanan_id' => $sktm->id, 'field_key' => 'nama_lengkap'],
            [
                'layanan_id' => $sktm->id,
                'name' => 'Nama Lengkap',
                'field_key' => 'nama_lengkap',
                'type' => LayananField::TYPE_TEXT,
                'is_required' => true,
                'sort_order' => 1,
            ]
        );

        if (! LayananPengajuan::where('layanan_id', $layanan->id)->exists()) {
            LayananPengajuan::create([
                'layanan_id' => $layanan->id,
                'registration_number' => LayananRegistrationNumber::generate($layanan),
                'data' => [
                    'nama_lengkap' => 'Budi Santoso',
                    'nik' => '1234567890123456',
                    'alamat' => 'Jl. Merdeka No. 10, Sindang Kelingi',
                    'telepon' => '081234567890',
                ],
                'status' => LayananPengajuan::STATUS_PENDING,
                'submitted_at' => now()->subHours(2),
            ]);
        }
    }
}
