<?php

namespace App\Http\View\Composers;

use App\Models\WebsiteCarousel;
use App\Models\WebsiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class WebsiteSettingComposer
{
    public function compose(View $view): void
    {
        $settings = WebsiteSetting::current();

        $view->with([
            'websiteSettings' => $settings,
            'websiteCarousels' => $this->activeCarousels($settings),
        ]);
    }

    private function activeCarousels(WebsiteSetting $settings): Collection
    {
        if (! $settings->carousel_enabled || ! Schema::hasTable('website_carousels')) {
            return collect();
        }

        return WebsiteCarousel::query()
            ->active()
            ->get()
            ->filter(fn (WebsiteCarousel $carousel) => $carousel->imageExists())
            ->values();
    }
}
