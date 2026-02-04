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
        Schema::create('donasi_danas', function (Blueprint $table) {
            $table->id();
            $table->decimal('target_rupiah', 15, 2)->nullable();
            $table->json('info_bank'); // Simpan No Rek & Nama Bank
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donasi_danas');
    }
};
