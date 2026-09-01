<?php

namespace Database\Seeders;

use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;

class WebsiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        WebsiteSetting::query()->firstOrCreate(
            ['id' => 1],
            WebsiteSetting::defaults()
        );

        WebsiteSetting::clearCache();
    }
}
