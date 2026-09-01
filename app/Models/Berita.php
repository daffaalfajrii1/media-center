<?php

namespace App\Models;

use App\Support\SeoUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Berita extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'berita_category_id',
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'thumbnail',
        'status',
        'views_count',
        'likes_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'views_count' => 'integer',
            'likes_count' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BeritaCategory::class, 'berita_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function categoryName(): string
    {
        return $this->category?->name ?? 'Tanpa Kategori';
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? asset('storage/'.$this->thumbnail) : null;
    }

    public function socialShareImageUrl(): string
    {
        if ($this->thumbnail) {
            return SeoUrl::absolute(asset('storage/'.$this->thumbnail))
                ?? WebsiteSetting::current()->defaultShareImageUrl();
        }

        if ($this->content && preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $this->content, $matches)) {
            return SeoUrl::absolute($matches[1])
                ?? WebsiteSetting::current()->defaultShareImageUrl();
        }

        return WebsiteSetting::current()->defaultShareImageUrl();
    }

    public function authorName(): string
    {
        return $this->author?->name ?? 'Redaksi '.$this->fallbackSiteName();
    }

    public function authorRoleLabel(): ?string
    {
        $role = $this->author?->roles->first()?->name;

        return match ($role) {
            User::ROLE_SUPER_ADMIN => 'Super Admin',
            User::ROLE_ADMIN => 'Administrator',
            User::ROLE_EDITOR => 'Editor Konten',
            User::ROLE_OPERATOR => 'Operator',
            default => $role ? ucfirst(str_replace('-', ' ', $role)) : null,
        };
    }

    public function authorAvatarUrl(): ?string
    {
        return $this->author?->avatarUrl();
    }

    public function authorInitial(): string
    {
        return $this->author?->avatarInitial() ?? 'R';
    }

    private function fallbackSiteName(): string
    {
        return WebsiteSetting::current()->siteName();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function publicUrl(): string
    {
        return route('public.berita.show', $this->slug);
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
