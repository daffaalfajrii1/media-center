<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('layanan_pengajuans', function (Blueprint $table) {
            $table->string('completion_method')->nullable()->after('admin_note');
            $table->string('result_file_path')->nullable()->after('completion_method');
            $table->text('completion_message')->nullable()->after('result_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('layanan_pengajuans', function (Blueprint $table) {
            $table->dropColumn(['completion_method', 'result_file_path', 'completion_message']);
        });
    }
};
