<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kuis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('tugas_id'); // relasi ke tugas (remedial)
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->integer('durasi'); // menit
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->integer('jumlah_soal')->default(10);
            $table->timestamps();

            $table->foreign('kelas_id')
                ->references('id')->on('kelas')
                ->cascadeOnDelete();

            $table->foreign('tugas_id')
                ->references('id')->on('tugas')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuis');
    }
};

