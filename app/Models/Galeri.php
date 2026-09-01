<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Galeri extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'event_date',
        'thumbnail',
        'status',
        'is_featured',
        'sort_order',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GaleriItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function photos(): HasMany
    {
        return $this->items()->where('type', GaleriItem::TYPE_PHOTO);
    }

    public function videos(): HasMany
    {
        return $this->items()->where('type', GaleriItem::TYPE_YOUTUBE);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function thumbnailUrl(): ?string
    {
        if ($this->thumbnail) {
            return asset('storage/'.$this->thumbnail);
        }

        $firstPhoto = $this->relationLoaded('items')
            ? $this->items->firstWhere('type', GaleriItem::TYPE_PHOTO)
            : $this->items()->where('type', GaleriItem::TYPE_PHOTO)->first();

        if ($firstPhoto?->image_path) {
            return $firstPhoto->imageUrl();
        }

        $firstVideo = $this->relationLoaded('items')
            ? $this->items->firstWhere('type', GaleriItem::TYPE_YOUTUBE)
            : $this->items()->where('type', GaleriItem::TYPE_YOUTUBE)->first();

        return $firstVideo?->thumbnailUrl();
    }

    public function itemsCountLabel(): string
    {
        $photos = $this->photos_count ?? $this->items()->where('type', GaleriItem::TYPE_PHOTO)->count();
        $videos = $this->videos_count ?? $this->items()->where('type', GaleriItem::TYPE_YOUTUBE)->count();

        $parts = [];
        if ($photos > 0) {
            $parts[] = $photos.' foto';
        }
        if ($videos > 0) {
            $parts[] = $videos.' video';
        }

        return $parts ? implode(', ', $parts) : 'Belum ada media';
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
