<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();

            // Data Umum
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('deskripsi');
            $table->string('banner_image')->nullable();
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->enum('status', ['buka', 'tutup', 'selesai'])->default('buka');

            // Creator
            $table->foreignId('admin_id')->constrained('users');

            // --- POLYMORPHIC FIELDS ---
            // Ini pengganti detail_id & detail_type
            // Nanti di database otomatis jadi kolom: detail_id (int) & detail_type (string)
            $table->morphs('detail');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};
