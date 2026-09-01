<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteCarousel;
use App\Models\WebsiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebsiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.pengaturan.edit', [
            'settings' => WebsiteSetting::current(),
            'carousels' => WebsiteCarousel::query()->ordered()->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = WebsiteSetting::query()->firstOrCreate(['id' => 1], WebsiteSetting::defaults());

        $validated = $request->validate($this->rules(), $this->messages(), $this->attributes());

        $data = collect($validated)
            ->except(['logo', 'remove_logo', 'favicon', 'remove_favicon'])
            ->all();

        $data['marquee_enabled'] = $request->boolean('marquee_enabled');
        $data['carousel_enabled'] = $request->boolean('carousel_enabled');
        $data['google_analytics_enabled'] = $request->boolean('google_analytics_enabled');

        if (! empty($data['google_analytics_measurement_id'])) {
            $data['google_analytics_measurement_id'] = strtoupper(trim((string) $data['google_analytics_measurement_id']));
        }

        if ($request->boolean('remove_logo') && $settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
            $data['logo_path'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('website/logo', 'public');
        }

        if ($request->boolean('remove_favicon') && $settings->favicon_path) {
            Storage::disk('public')->delete($settings->favicon_path);
            $data['favicon_path'] = null;
        } elseif ($request->hasFile('favicon')) {
            if ($settings->favicon_path) {
                Storage::disk('public')->delete($settings->favicon_path);
            }
            $data['favicon_path'] = $request->file('favicon')->store('website/favicon', 'public');
        }

        $settings->update($data);
        WebsiteSetting::clearCache();

        return redirect()
            ->route('admin.pengaturan.edit')
            ->with('success', 'Pengaturan website berhasil disimpan.');
    }

    public function storeCarousel(Request $request): RedirectResponse
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'max:'.WebsiteCarousel::MAX_FILE_KB],
            'titles' => ['nullable', 'array'],
            'titles.*' => ['nullable', 'string', 'max:120'],
            'captions' => ['nullable', 'array'],
            'captions.*' => ['nullable', 'string', 'max:255'],
            'link_urls' => ['nullable', 'array'],
            'link_urls.*' => ['nullable', 'url', 'max:500'],
        ], [], [
            'images' => 'gambar carousel',
            'images.*' => 'gambar carousel',
            'titles.*' => 'judul slide',
            'captions.*' => 'keterangan slide',
            'link_urls.*' => 'tautan slide',
        ]);

        $maxSort = (int) WebsiteCarousel::query()->max('sort_order');
        $uploaded = 0;

        foreach ($request->file('images', []) as $index => $file) {
            $path = $file->store('website/carousel', 'public');
            $maxSort++;

            WebsiteCarousel::create([
                'image_path' => $path,
                'title' => $request->input("titles.{$index}") ?: null,
                'caption' => $request->input("captions.{$index}") ?: null,
                'link_url' => $request->input("link_urls.{$index}") ?: null,
                'sort_order' => $maxSort,
            ]);

            $uploaded++;
        }

        WebsiteSetting::clearCache();

        return redirect()
            ->route('admin.pengaturan.edit')
            ->with('success', "{$uploaded} gambar carousel berhasil ditambahkan.");
    }

    public function destroyCarousel(WebsiteCarousel $carousel): RedirectResponse
    {
        Storage::disk('public')->delete($carousel->image_path);
        $carousel->delete();

        WebsiteSetting::clearCache();

        return redirect()
            ->route('admin.pengaturan.edit')
            ->with('success', 'Gambar carousel berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:120'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'favicon' => ['nullable', 'image', 'max:1024'],
            'remove_favicon' => ['nullable', 'boolean'],
            'carousel_enabled' => ['nullable', 'boolean'],
            'carousel_duration' => [
                'required',
                'integer',
                'min:'.WebsiteSetting::CAROUSEL_DURATION_MIN,
                'max:'.WebsiteSetting::CAROUSEL_DURATION_MAX,
            ],
            'marquee_enabled' => ['nullable', 'boolean'],
            'marquee_mode' => ['required', Rule::in([
                WebsiteSetting::MARQUEE_OFF,
                WebsiteSetting::MARQUEE_CUSTOM,
                WebsiteSetting::MARQUEE_PENGUMUMAN,
            ])],
            'marquee_text' => ['nullable', 'string', 'max:2000'],
            'google_maps_embed' => ['nullable', 'string', 'max:5000'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:120'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
            'footer_text' => ['nullable', 'string', 'max:500'],
            'google_analytics_enabled' => ['nullable', 'boolean'],
            'google_analytics_measurement_id' => [
                'nullable',
                'string',
                'max:32',
                Rule::requiredIf(fn () => request()->boolean('google_analytics_enabled')),
                'regex:/^G-[A-Z0-9]+$/i',
            ],
        ];
    }

    /** @return array<string, string> */
    private function messages(): array
    {
        return [
            'site_name.required' => 'Nama website wajib diisi.',
            'carousel_duration.min' => 'Durasi carousel minimal '.WebsiteSetting::CAROUSEL_DURATION_MIN.' detik.',
            'carousel_duration.max' => 'Durasi carousel maksimal '.WebsiteSetting::CAROUSEL_DURATION_MAX.' detik.',
            'contact_email.email' => 'Format email kontak tidak valid.',
            'social_facebook.url' => 'URL Facebook tidak valid.',
            'social_instagram.url' => 'URL Instagram tidak valid.',
            'social_youtube.url' => 'URL YouTube tidak valid.',
            'google_analytics_measurement_id.regex' => 'Measurement ID harus format GA4, contoh: G-XXXXXXXXXX.',
        ];
    }

    /** @return array<string, string> */
    private function attributes(): array
    {
        return [
            'site_name' => 'nama website',
            'site_tagline' => 'tagline',
            'logo' => 'logo',
            'favicon' => 'favicon',
            'carousel_duration' => 'durasi carousel',
            'marquee_text' => 'teks marquee',
            'google_maps_embed' => 'embed peta Google',
            'contact_address' => 'alamat',
            'contact_phone' => 'telepon',
            'contact_email' => 'email',
            'contact_whatsapp' => 'WhatsApp',
            'footer_text' => 'teks footer',
            'google_analytics_measurement_id' => 'Google Analytics Measurement ID',
        ];
    }
}
