<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rb_guru', function (Blueprint $table) {
            $table->string('nip', 30)->primary();
            $table->string('password', 255);
            $table->string('nama_guru', 150);
            $table->integer('id_jenis_kelamin')->nullable();
            $table->string('tempat_lahir', 150)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nik', 50)->nullable();
            $table->string('niy_nigk', 50)->nullable();
            $table->string('nuptk', 50)->nullable();
            $table->integer('id_status_kepegawaian')->nullable();
            $table->integer('id_jenis_ptk')->nullable();
            $table->string('pengawas_bidang_studi', 150)->nullable();
            $table->integer('id_agama')->nullable();
            $table->string('alamat_jalan', 255)->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('nama_dusun', 100)->nullable();
            $table->string('desa_kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->integer('kode_pos')->nullable();
            $table->string('telepon', 15)->nullable();
            $table->string('hp', 15)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('tugas_tambahan', 100)->nullable();
            $table->integer('id_status_keaktifan')->nullable();
            $table->string('sk_cpns', 150)->nullable();
            $table->date('tanggal_cpns')->nullable();
            $table->string('sk_pengangkatan', 150)->nullable();
            $table->date('tmt_pengangkatan')->nullable();
            $table->string('lembaga_pengangkatan', 150)->nullable();
            $table->integer('id_golongan')->nullable();
            $table->string('keahlian_laboratorium', 150)->nullable();
            $table->string('sumber_gaji', 150)->nullable();
            $table->string('nama_ibu_kandung', 100)->nullable();
            $table->integer('id_status_pernikahan')->nullable();
            $table->string('nama_suami_istri', 100)->nullable();
            $table->string('nip_suami_istri', 30)->nullable();
            $table->string('pekerjaan_suami_istri', 100)->nullable();
            $table->date('tmt_pns')->nullable();
            $table->string('lisensi_kepsek', 20)->nullable();
            $table->integer('jumlah_sekolah_binaan')->nullable();
            $table->string('diklat_kepengawasan', 20)->nullable();
            $table->string('mampu_handle_kk', 20)->nullable();
            $table->string('keahlian_breile', 20)->nullable();
            $table->string('keahlian_bahasa_isyarat', 20)->nullable();
            $table->string('npwp', 50)->nullable();
            $table->string('kewarganegaraan', 50)->nullable();
            $table->string('foto', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rb_guru');
    }
};
