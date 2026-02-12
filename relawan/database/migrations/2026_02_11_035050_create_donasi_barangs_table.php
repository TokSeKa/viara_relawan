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
        Schema::create('donasi_barangs', function (Blueprint $table) {
            $table->id();
            $table->string('target_item');   // Misal: Beras, Selimut
            $table->integer('target_jumlah');
            $table->string('lokasi_kumpul');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donasi_barangs');
    }
};
