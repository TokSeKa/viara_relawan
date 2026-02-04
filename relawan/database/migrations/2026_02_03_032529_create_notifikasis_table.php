<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('pesan');

            // Tipe Notifikasi (Warna)
            $table->enum('type', ['info', 'warning', 'danger', 'success'])->default('info');

            // Target Audiens
            $table->enum('target_audience', ['all', 'kegiatan', 'tag']);

            // --- PERUBAHAN DISINI ---
            // Kita pakai TEXT agar bisa simpan multiple ID (contoh: "1,2,3")
            // Tidak perlu constrained() karena ini bukan foreign key murni lagi
            $table->text('kegiatan_id')->nullable(); 
            $table->text('tag_id')->nullable();

            // Waktu Kadaluarsa
            $table->dateTime('expires_at')->nullable();

            // Pembuat Notif (Admin)
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};