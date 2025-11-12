<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kelas_kontens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained()->cascadeOnDelete();
            $table->string('judul');
            $table->enum('tipe', ['file', 'link', 'teks']);
            $table->text('isi')->nullable();
            $table->string('file_path')->nullable();
            $table->string('uploaded_by', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_kontens');
    }
};