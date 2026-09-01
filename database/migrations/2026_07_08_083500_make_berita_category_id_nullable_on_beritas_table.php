<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beritas', function (Blueprint $table) {
            $table->dropForeign(['berita_category_id']);
        });

        Schema::table('beritas', function (Blueprint $table) {
            $table->unsignedBigInteger('berita_category_id')->nullable()->change();
            $table->foreign('berita_category_id')
                ->references('id')
                ->on('berita_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('beritas', function (Blueprint $table) {
            $table->dropForeign(['berita_category_id']);
        });

        Schema::table('beritas', function (Blueprint $table) {
            $table->unsignedBigInteger('berita_category_id')->nullable(false)->change();
            $table->foreign('berita_category_id')
                ->references('id')
                ->on('berita_categories')
                ->cascadeOnDelete();
        });
    }
};
