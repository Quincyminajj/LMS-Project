<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas_pengumpulans', function (Blueprint $table) {
            // Rename feedback ke catatan_guru (TETAP PAKAI siswa_nisn)
            if (
                Schema::hasColumn('tugas_pengumpulans', 'feedback') &&
                !Schema::hasColumn('tugas_pengumpulans', 'catatan_guru')
            ) {
                $table->renameColumn('feedback', 'catatan_guru');
            }

            // Tambahkan kolom jawaban
            if (!Schema::hasColumn('tugas_pengumpulans', 'jawaban')) {
                $table->text('jawaban')->nullable()->after('siswa_nisn');
            }

            // Hapus kolom yang tidak dipakai
            $columnsToRemove = ['tipe', 'isi', 'dikumpul_pada', 'dinilai_pada'];
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('tugas_pengumpulans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('tugas_pengumpulans', function (Blueprint $table) {
            if (Schema::hasColumn('tugas_pengumpulans', 'jawaban')) {
                $table->dropColumn('jawaban');
            }

            if (Schema::hasColumn('tugas_pengumpulans', 'catatan_guru')) {
                $table->renameColumn('catatan_guru', 'feedback');
            }

            // Re-add kolom yang dihapus
            $table->enum('tipe', ['file', 'link', 'teks']);
            $table->text('isi')->nullable();
            $table->timestamp('dikumpul_pada')->useCurrent();
            $table->timestamp('dinilai_pada')->nullable();
        });
    }
};
