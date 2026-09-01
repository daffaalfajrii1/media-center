<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WebsiteCarousel extends Model
{
    public const RECOMMENDED_WIDTH = 1920;

    public const RECOMMENDED_HEIGHT = 600;

    public const MAX_FILE_KB = 3072;

    protected $fillable = [
        'image_path',
        'title',
        'caption',
        'link_url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function imageUrl(): string
    {
        return asset('storage/'.$this->image_path);
    }

    public function imageExists(): bool
    {
        return $this->image_path !== null
            && Storage::disk('public')->exists($this->image_path);
    }

    public static function sizeGuideline(): string
    {
        return self::RECOMMENDED_WIDTH.' × '.self::RECOMMENDED_HEIGHT.' px (rasio 16:5)';
    }
}
