<?php

namespace App\Models;

use App\Support\SeoUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class WebsiteSetting extends Model
{
    public const MARQUEE_OFF = 'off';

    public const MARQUEE_CUSTOM = 'custom';

    public const MARQUEE_PENGUMUMAN = 'pengumuman';

    public const CAROUSEL_DURATION_MIN = 2;

    public const CAROUSEL_DURATION_MAX = 30;

    public const CAROUSEL_DURATION_DEFAULT = 5;

    protected $fillable = [
        'site_name',
        'site_tagline',
        'logo_path',
        'favicon_path',
        'carousel_enabled',
        'carousel_duration',
        'marquee_enabled',
        'marquee_mode',
        'marquee_text',
        'google_maps_embed',
        'contact_address',
        'contact_phone',
        'contact_email',
        'contact_whatsapp',
        'social_facebook',
        'social_instagram',
        'social_youtube',
        'footer_text',
        'google_analytics_enabled',
        'google_analytics_measurement_id',
    ];

    protected function casts(): array
    {
        return [
            'marquee_enabled' => 'boolean',
            'carousel_enabled' => 'boolean',
            'carousel_duration' => 'integer',
            'google_analytics_enabled' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return Cache::remember('website_settings', 3600, function () {
            if (! Schema::hasTable('website_settings')) {
                return new self(self::defaults());
            }

            return self::query()->first() ?? self::query()->create(self::defaults());
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('website_settings');
    }

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'site_name' => 'Kecamatan Sindang Kelingi',
            'site_tagline' => 'Website Resmi Pemerintah Kecamatan',
            'marquee_enabled' => true,
            'marquee_mode' => self::MARQUEE_CUSTOM,
            'marquee_text' => 'Selamat datang di website resmi Kecamatan Sindang Kelingi.',
            'contact_address' => 'Jl. Contoh No. 1, Sindang Kelingi, Rejang Lebong, Bengkulu',
            'contact_phone' => '(0732) 000000',
            'contact_email' => 'info@sindangkelingi.go.id',
            'contact_whatsapp' => '6281200000000',
            'footer_text' => 'Website Resmi Pemerintah Kecamatan Sindang Kelingi.',
            'carousel_enabled' => true,
            'carousel_duration' => self::CAROUSEL_DURATION_DEFAULT,
            'google_analytics_enabled' => false,
        ];
    }

    public function carouselDurationMs(): int
    {
        $seconds = (int) ($this->carousel_duration ?: self::CAROUSEL_DURATION_DEFAULT);

        return max(self::CAROUSEL_DURATION_MIN, min(self::CAROUSEL_DURATION_MAX, $seconds)) * 1000;
    }

    public function siteName(): string
    {
        return $this->site_name ?: config('app.name', 'Kecamatan Sindang Kelingi');
    }

    public function hasLogo(): bool
    {
        if (! $this->logo_path) {
            return false;
        }

        return Storage::disk('public')->exists($this->logo_path);
    }

    public function logoUrl(): ?string
    {
        if (! $this->hasLogo()) {
            return null;
        }

        return asset('storage/'.$this->logo_path);
    }

    /** @return array{primary: string, secondary: string} */
    public function logoTextLines(): array
    {
        $name = trim($this->siteName());
        $parts = preg_split('/\s+/', $name) ?: [];

        if (count($parts) >= 3) {
            return [
                'primary' => implode(' ', array_slice($parts, 0, -2)),
                'secondary' => implode(' ', array_slice($parts, -2)),
            ];
        }

        if (count($parts) === 2) {
            return [
                'primary' => $parts[0],
                'secondary' => $parts[1],
            ];
        }

        return [
            'primary' => $name,
            'secondary' => trim((string) ($this->site_tagline ?: 'Media Center')),
        ];
    }

    public function faviconUrl(): ?string
    {
        if ($this->favicon_path) {
            return asset('storage/'.$this->favicon_path);
        }

        return $this->logoUrl();
    }

    public function mapsIframeSrc(): ?string
    {
        $embed = trim((string) $this->google_maps_embed);
        if ($embed === '') {
            return null;
        }

        if (preg_match('/src=["\']([^"\']+)["\']/i', $embed, $matches)) {
            return $matches[1];
        }

        if (filter_var($embed, FILTER_VALIDATE_URL)) {
            return $embed;
        }

        return null;
    }

    /** @return list<string> */
    public function marqueeItems(): array
    {
        if (! $this->marquee_enabled || $this->marquee_mode === self::MARQUEE_OFF) {
            return [];
        }

        if ($this->marquee_mode === self::MARQUEE_PENGUMUMAN) {
            if (! Schema::hasTable('pengumumans')) {
                return [];
            }

            return Pengumuman::query()
                ->published()
                ->orderByDesc('published_at')
                ->limit(8)
                ->pluck('title')
                ->all();
        }

        if ($this->marquee_text) {
            $lines = preg_split('/\r\n|\r|\n|\|/', $this->marquee_text) ?: [];

            return array_values(array_filter(array_map('trim', $lines)));
        }

        return [];
    }

    public function whatsappUrl(): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $this->contact_whatsapp);
        if ($number === '') {
            return null;
        }

        return 'https://wa.me/'.$number;
    }

    public function googleAnalyticsMeasurementId(): ?string
    {
        $id = strtoupper(trim((string) $this->google_analytics_measurement_id));

        if ($id === '' || ! preg_match('/^G-[A-Z0-9]+$/', $id)) {
            return null;
        }

        return $id;
    }

    public function googleAnalyticsEnabled(): bool
    {
        return (bool) $this->google_analytics_enabled && $this->googleAnalyticsMeasurementId() !== null;
    }

    public function defaultShareImageUrl(): string
    {
        return SeoUrl::absolute($this->logoUrl() ?: asset('binduz/assets/images/hero-1.jpg'))
            ?? config('app.url').'/binduz/assets/images/hero-1.jpg';
    }
}
