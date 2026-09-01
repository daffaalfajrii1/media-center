<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galeri_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('galeri_id')->constrained('galeris')->cascadeOnDelete();
            $table->string('type');
            $table->string('title')->nullable();
            $table->string('image_path')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('youtube_video_id')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galeri_items');
    }
};
