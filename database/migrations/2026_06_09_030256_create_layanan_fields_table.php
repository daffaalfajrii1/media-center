<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_id')->constrained('layanans')->cascadeOnDelete();
            $table->string('name');
            $table->string('field_key');
            $table->string('type');
            $table->json('options')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('help_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['layanan_id', 'field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_fields');
    }
};
