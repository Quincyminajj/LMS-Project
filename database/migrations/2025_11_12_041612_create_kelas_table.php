<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kelas', 10)->unique();
            $table->string('nama_kelas');
            $table->string('guru_nip', 20);
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'arsip'])->default('aktif');
            $table->timestamps();

            $table->index('guru_nip');
            $table->index('kode_kelas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};