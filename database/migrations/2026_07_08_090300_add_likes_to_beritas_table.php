<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beritas', function (Blueprint $table) {
            $table->unsignedBigInteger('likes_count')->default(0)->after('views_count');
        });

        Schema::create('berita_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berita_id')->constrained('beritas')->cascadeOnDelete();
            $table->string('visitor_token', 64);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['berita_id', 'visitor_token']);
            $table->index('visitor_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita_likes');

        Schema::table('beritas', function (Blueprint $table) {
            $table->dropColumn('likes_count');
        });
    }
};
