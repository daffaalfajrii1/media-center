<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    public const GROUP_PROFIL = 'profil';

    public const MENU_GROUPS = [
        self::GROUP_PROFIL => 'Profil',
    ];

    protected $fillable = [
        'title',
        'slug',
        'menu_group',
        'excerpt',
        'content',
        'is_published',
        'sort_order',
        'user_id',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeProfil(Builder $query): Builder
    {
        return $query->where('menu_group', self::GROUP_PROFIL);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public static function uniqueSlug(string $title, string $menuGroup, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (
            static::query()
                ->where('menu_group', $menuGroup)
                ->where('slug', $slug)
                ->when($ignoreId, fn (Builder $q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
