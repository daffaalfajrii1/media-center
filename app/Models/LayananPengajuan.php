<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LayananPengajuan extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_REJECTED = 'rejected';

    public const COMPLETION_FILE_UPLOAD = 'file_upload';

    public const COMPLETION_OFFICE_PICKUP = 'office_pickup';

    public const STATUSES = [
        self::STATUS_PENDING => 'Menunggu',
        self::STATUS_PROCESSING => 'Diproses',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_REJECTED => 'Ditolak',
    ];

    public const COMPLETION_METHODS = [
        self::COMPLETION_FILE_UPLOAD => 'Upload hasil (unduh online)',
        self::COMPLETION_OFFICE_PICKUP => 'Ambil di kantor kecamatan',
    ];

    protected $table = 'layanan_pengajuans';

    protected $fillable = [
        'layanan_id',
        'registration_number',
        'data',
        'status',
        'admin_note',
        'completion_method',
        'result_file_path',
        'completion_message',
        'processed_by',
        'submitted_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'submitted_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getFieldValue(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function displayValueForField(LayananField $field): string
    {
        $value = $this->getFieldValue($field->field_key);

        if ($value === null || $value === '') {
            return '—';
        }

        if ($field->isFileType()) {
            return is_string($value) ? basename($value) : '—';
        }

        if ($field->type === LayananField::TYPE_CHECKBOX) {
            return $value ? 'Ya' : 'Tidak';
        }

        if (is_array($value)) {
            return implode(', ', $value);
        }

        return (string) $value;
    }

    public function fileUrl(string $key): ?string
    {
        $path = $this->getFieldValue($key);

        return is_string($path) && $path !== '' ? asset('storage/'.$path) : null;
    }

    public function completionMethodLabel(): ?string
    {
        return $this->completion_method
            ? (self::COMPLETION_METHODS[$this->completion_method] ?? $this->completion_method)
            : null;
    }

    public function resultFileUrl(): ?string
    {
        return $this->result_file_path ? asset('storage/'.$this->result_file_path) : null;
    }

    public function hasUploadedResult(): bool
    {
        return $this->completion_method === self::COMPLETION_FILE_UPLOAD && $this->result_file_path;
    }

    public function isOfficePickup(): bool
    {
        return $this->completion_method === self::COMPLETION_OFFICE_PICKUP;
    }

    public function deleteStoredFiles(): void
    {
        if ($this->result_file_path) {
            Storage::disk('public')->delete($this->result_file_path);
        }

        foreach ($this->data ?? [] as $value) {
            if (is_string($value) && str_starts_with($value, 'layanan-pengajuans/')) {
                Storage::disk('public')->delete($value);
            }
        }

        Storage::disk('public')->deleteDirectory("layanan-pengajuans/{$this->id}");
    }
}
