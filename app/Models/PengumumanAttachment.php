<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PengumumanAttachment extends Model
{
    protected $fillable = [
        'pengumuman_id',
        'title',
        'file_path',
        'original_name',
        'file_size',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function pengumuman(): BelongsTo
    {
        return $this->belongsTo(Pengumuman::class);
    }

    public function downloadUrl(): string
    {
        return asset('storage/'.$this->file_path);
    }

    public function displayTitle(): string
    {
        return $this->title ?: $this->original_name;
    }

    public function formattedFileSize(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1).' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    public function deleteFile(): void
    {
        if ($this->file_path) {
            Storage::disk('public')->delete($this->file_path);
        }
    }
}
