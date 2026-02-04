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
        Schema::create('donasi_darahs', function (Blueprint $table) {
            $table->id();
            $table->integer('target_kantong');
            $table->json('golongan_darah_needed'); // ["A", "AB"]
            $table->string('lokasi_pmi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donasi_darahs');
    }
};
