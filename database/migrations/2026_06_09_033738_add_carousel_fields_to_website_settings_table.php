<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->boolean('carousel_enabled')->default(true)->after('favicon_path');
            $table->unsignedSmallInteger('carousel_duration')->default(5)->after('carousel_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn(['carousel_enabled', 'carousel_duration']);
        });
    }
};
