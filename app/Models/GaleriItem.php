<?php

namespace App\Models;

use App\Support\Youtube;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaleriItem extends Model
{
    public const TYPE_PHOTO = 'photo';

    public const TYPE_YOUTUBE = 'youtube';

    protected $fillable = [
        'galeri_id',
        'type',
        'title',
        'image_path',
        'youtube_url',
        'youtube_video_id',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function galeri(): BelongsTo
    {
        return $this->belongsTo(Galeri::class);
    }

    public function isPhoto(): bool
    {
        return $this->type === self::TYPE_PHOTO;
    }

    public function isYoutube(): bool
    {
        return $this->type === self::TYPE_YOUTUBE;
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }

    public function thumbnailUrl(): ?string
    {
        if ($this->isPhoto()) {
            return $this->imageUrl();
        }

        return Youtube::thumbnailUrl($this->youtube_video_id);
    }

    public function embedUrl(): ?string
    {
        return Youtube::embedUrl($this->youtube_video_id);
    }
}
