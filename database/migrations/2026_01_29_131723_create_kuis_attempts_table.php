<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kuis_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kuis_id');
            $table->string('siswa_nisn', 20);

            $table->dateTime('mulai_pada')->nullable();
            $table->dateTime('selesai_pada')->nullable();
            $table->integer('durasi')->nullable(); // menit

            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->enum('status', ['Lulus', 'Tidak Lulus'])->nullable();

            $table->timestamps();

            $table->unique(['kuis_id', 'siswa_nisn']); // 1x attempt

            $table->foreign('kuis_id')
                ->references('id')->on('kuis')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuis_attempts');
    }
};

