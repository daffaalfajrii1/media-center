<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LayananField extends Model
{
    public const TYPE_TEXT = 'text';

    public const TYPE_TEXTAREA = 'textarea';

    public const TYPE_EMAIL = 'email';

    public const TYPE_TEL = 'tel';

    public const TYPE_NUMBER = 'number';

    public const TYPE_DATE = 'date';

    public const TYPE_SELECT = 'select';

    public const TYPE_RADIO = 'radio';

    public const TYPE_CHECKBOX = 'checkbox';

    public const TYPE_FILE = 'file';

    public const TYPE_PDF = 'pdf';

    public const TYPE_IMAGE = 'image';

    public const TYPES = [
        self::TYPE_TEXT => 'Teks Singkat',
        self::TYPE_TEXTAREA => 'Teks Panjang',
        self::TYPE_EMAIL => 'Email',
        self::TYPE_TEL => 'Nomor Telepon',
        self::TYPE_NUMBER => 'Angka',
        self::TYPE_DATE => 'Tanggal',
        self::TYPE_SELECT => 'Pilihan (Dropdown)',
        self::TYPE_RADIO => 'Pilihan (Radio)',
        self::TYPE_CHECKBOX => 'Centang (Ya/Tidak)',
        self::TYPE_FILE => 'Upload File',
        self::TYPE_PDF => 'Upload PDF',
        self::TYPE_IMAGE => 'Upload Gambar',
    ];

    protected $fillable = [
        'layanan_id',
        'name',
        'field_key',
        'type',
        'options',
        'placeholder',
        'help_text',
        'is_required',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function isFileType(): bool
    {
        return in_array($this->type, [self::TYPE_FILE, self::TYPE_PDF, self::TYPE_IMAGE], true);
    }

    public function hasOptions(): bool
    {
        return in_array($this->type, [self::TYPE_SELECT, self::TYPE_RADIO], true);
    }

    public static function uniqueFieldKey(int $layananId, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name, '_') ?: 'field';
        $key = $base;
        $counter = 1;

        while (
            static::query()
                ->where('layanan_id', $layananId)
                ->where('field_key', $key)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $key = $base.'_'.$counter;
            $counter++;
        }

        return $key;
    }

    public static function parseOptions(?string $raw): ?array
    {
        if (! $raw) {
            return null;
        }

        $options = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw) ?: [])));

        return $options ?: null;
    }
}
