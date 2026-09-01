<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Pengumuman extends Model
{
    protected $table = 'pengumumans';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const PRIORITY_NORMAL = 'normal';

    public const PRIORITY_PENTING = 'penting';

    public const PRIORITIES = [
        self::PRIORITY_NORMAL => 'Normal',
        self::PRIORITY_PENTING => 'Penting',
    ];

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PUBLISHED => 'Terbit',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'priority',
        'is_featured',
        'valid_until',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'valid_until' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PengumumanAttachment::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isPenting(): bool
    {
        return $this->priority === self::PRIORITY_PENTING;
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    public function priorityLabel(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function attachmentsCountLabel(): string
    {
        $count = $this->attachments_count ?? $this->attachments()->count();

        return $count > 0 ? $count.' file PDF' : 'Tanpa lampiran';
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
}
