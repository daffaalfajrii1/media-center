<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Layanan extends Model
{
    protected $table = 'layanans';

    protected $fillable = [
        'user_id',
        'code',
        'title',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(LayananField::class)->orderBy('sort_order')->orderBy('id');
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(LayananPengajuan::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function registrationPrefix(): string
    {
        return strtoupper($this->code);
    }

    public static function generateCode(): string
    {
        $next = static::count() + 1;

        do {
            $code = 'LYN'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (static::query()->where('code', $code)->exists());

        return $code;
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
