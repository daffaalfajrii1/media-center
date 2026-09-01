<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Agenda extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_CANCELLED = 'cancelled';

    public const TYPE_RAPAT = 'rapat';

    public const TYPE_PELATIHAN = 'pelatihan';

    public const TYPE_SOSIALISASI = 'sosialisasi';

    public const TYPE_KEGIATAN_SOSIAL = 'kegiatan_sosial';

    public const TYPE_KUNJUNGAN = 'kunjungan';

    public const TYPE_LAINNYA = 'lainnya';

    public const EVENT_TYPES = [
        self::TYPE_RAPAT => 'Rapat / Koordinasi',
        self::TYPE_PELATIHAN => 'Pelatihan / Bimbingan',
        self::TYPE_SOSIALISASI => 'Sosialisasi / Seminar',
        self::TYPE_KEGIATAN_SOSIAL => 'Kegiatan Sosial / Bakti',
        self::TYPE_KUNJUNGAN => 'Kunjungan Kerja',
        self::TYPE_LAINNYA => 'Lainnya',
    ];

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PUBLISHED => 'Terbit',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'event_type',
        'excerpt',
        'content',
        'event_date',
        'start_time',
        'end_time',
        'location',
        'location_address',
        'is_online',
        'online_link',
        'organizer',
        'contact_name',
        'contact_phone',
        'registration_url',
        'thumbnail',
        'status',
        'is_featured',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'is_online' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query
            ->published()
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->orderBy('start_time');
    }

    public function scopePast(Builder $query): Builder
    {
        return $query
            ->published()
            ->where('event_date', '<', now()->toDateString())
            ->orderByDesc('event_date')
            ->orderByDesc('start_time');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isUpcoming(): bool
    {
        return $this->isPublished()
            && $this->event_date->toDateString() >= now()->toDateString();
    }

    public function eventTypeLabel(): string
    {
        return self::EVENT_TYPES[$this->event_type] ?? $this->event_type;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? asset('storage/'.$this->thumbnail) : null;
    }

    public function formattedTimeRange(): string
    {
        $start = $this->formatTime($this->start_time);
        $end = $this->end_time ? $this->formatTime($this->end_time) : null;

        return $end ? "{$start} – {$end} WIB" : "{$start} WIB";
    }

    public function formattedEventDate(): string
    {
        return $this->event_date->translatedFormat('l, d F Y');
    }

    public function locationDisplay(): string
    {
        if ($this->is_online) {
            return $this->location ?: 'Kegiatan Daring';
        }

        return $this->location ?: '—';
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function formatTime(?string $time): string
    {
        if (! $time) {
            return '—';
        }

        return Carbon::parse($time)->format('H:i');
    }
}
