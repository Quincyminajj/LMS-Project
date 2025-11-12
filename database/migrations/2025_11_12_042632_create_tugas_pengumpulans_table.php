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
        Schema::create('tugas_pengumpulans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained()->cascadeOnDelete();
            $table->string('siswa_nisn', 20);
            $table->enum('tipe', ['file', 'link', 'teks']);
            $table->text('isi')->nullable();
            $table->string('file_path')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->string('dinilai_oleh', 20)->nullable();
            $table->timestamp('dikumpul_pada')->useCurrent();
            $table->timestamp('dinilai_pada')->nullable();
            $table->unique(['tugas_id', 'siswa_nisn']);
            $table->index('nilai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas_pengumpulans');
    }
};
