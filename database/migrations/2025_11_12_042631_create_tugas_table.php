<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained()->cascadeOnDelete();
            $table->string('judul');
            $table->decimal('nilai_maksimal', 5, 2)->default(100.00);
            $table->dateTime('deadline');
            $table->text('deskripsi')->nullable();
            $table->string('file_contoh')->nullable();
            $table->string('created_by', 20);
            $table->timestamps();

            $table->index('deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};