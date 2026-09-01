<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita_daily_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berita_id')->constrained('beritas')->cascadeOnDelete();
            $table->date('view_date');
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();

            $table->unique(['berita_id', 'view_date']);
            $table->index('view_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita_daily_views');
    }
};
