<?php

namespace App\Support;

use App\Models\Layanan;
use App\Models\LayananPengajuan;

class LayananRegistrationNumber
{
    public static function generate(Layanan $layanan): string
    {
        $year = now()->year;
        $prefix = $layanan->registrationPrefix();

        $lastSequence = LayananPengajuan::query()
            ->where('layanan_id', $layanan->id)
            ->where('registration_number', 'like', "{$prefix}-{$year}-%")
            ->orderByDesc('id')
            ->value('registration_number');

        $next = 1;
        if ($lastSequence && preg_match('/-(\d+)$/', $lastSequence, $matches)) {
            $next = (int) $matches[1] + 1;
        }

        return sprintf('%s-%d-%06d', $prefix, $year, $next);
    }
}
