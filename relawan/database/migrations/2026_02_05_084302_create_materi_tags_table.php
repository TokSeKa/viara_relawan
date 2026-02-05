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
        Schema::create('materi_tags', function (Blueprint $table) {
            // ID Materi (Foreign Key)
            $table->foreignId('materi_id')->constrained('materis')->cascadeOnDelete();

            // ID Tag (Foreign Key)
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();

            // Gabungan Primary Key biar tidak ada duplikat (Materi A punya Tag B dua kali)
            $table->primary(['materi_id', 'tag_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materi_tags');
    }
};
