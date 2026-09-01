<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_id')->constrained('layanans')->cascadeOnDelete();
            $table->string('registration_number')->unique();
            $table->json('data');
            $table->string('status')->default('pending');
            $table->text('admin_note')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_pengajuans');
    }
};
