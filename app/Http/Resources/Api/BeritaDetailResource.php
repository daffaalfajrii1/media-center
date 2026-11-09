<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;

class BeritaDetailResource extends BeritaResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'content' => $this->content,
            'likes_count' => $this->likes_count,
        ]);
    }
}
