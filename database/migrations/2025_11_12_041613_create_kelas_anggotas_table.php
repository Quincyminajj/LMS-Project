<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kelas_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained()->cascadeOnDelete();
            $table->string('siswa_nisn', 20);
            $table->timestamp('joined_at')->useCurrent();
            $table->unique(['kelas_id', 'siswa_nisn']);
            $table->index('siswa_nisn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_anggotas');
    }
};