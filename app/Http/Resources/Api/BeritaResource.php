<?php

namespace App\Http\Resources\Api;

use App\Support\SeoUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Berita */
class BeritaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $thumbnail = SeoUrl::absolute($this->thumbnailUrl());

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) $this->content), 160, ''),
            'thumbnail' => $thumbnail,
            'featured_image' => $thumbnail,
            'category' => $this->whenLoaded('category', fn () => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null),
            'author' => $this->whenLoaded('author', fn () => [
                'name' => $this->authorName(),
            ]),
            'published_at' => $this->published_at?->toIso8601String(),
            'published_at_formatted' => $this->published_at?->timezone(config('app.timezone'))->format('d/m/Y'),
            'views_count' => $this->views_count,
            'url' => $this->publicUrl(),
            'read_more_url' => $this->publicUrl(),
        ];
    }
}
