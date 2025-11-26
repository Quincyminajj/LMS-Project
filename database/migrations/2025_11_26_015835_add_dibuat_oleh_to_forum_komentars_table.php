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
        Schema::table('forum_komentars', function (Blueprint $table) {
            // Tambahkan kolom dibuat_oleh jika belum ada
            if (!Schema::hasColumn('forum_komentars', 'dibuat_oleh')) {
                $table->string('dibuat_oleh', 100)->nullable()->after('isi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_komentars', function (Blueprint $table) {
            $table->dropColumn('dibuat_oleh');
        });
    }
};
