<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forum_komentars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_id')->constrained()->cascadeOnDelete();
            $table->string('pengirim_nisn_nip', 20);
            $table->enum('pengirim_tipe', ['siswa', 'guru']);
            $table->text('isi');
            $table->foreignId('parent_id')->nullable()->constrained('forum_komentar')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['pengirim_nisn_nip', 'pengirim_tipe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_komentars');
    }
};