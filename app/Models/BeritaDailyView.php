<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeritaDailyView extends Model
{
    protected $fillable = [
        'berita_id',
        'view_date',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'view_date' => 'date',
            'views_count' => 'integer',
        ];
    }

    public function berita(): BelongsTo
    {
        return $this->belongsTo(Berita::class);
    }

    public static function record(int $beritaId, ?string $date = null): void
    {
        $viewDate = Carbon::parse($date ?? now())->toDateString();

        $existing = static::query()
            ->where('berita_id', $beritaId)
            ->whereDate('view_date', $viewDate)
            ->first();

        if ($existing) {
            $existing->increment('views_count');

            return;
        }

        static::query()->create([
            'berita_id' => $beritaId,
            'view_date' => $viewDate,
            'views_count' => 1,
        ]);
    }
}
