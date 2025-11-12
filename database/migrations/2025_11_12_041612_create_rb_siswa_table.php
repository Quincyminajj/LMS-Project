<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rb_siswa', function (Blueprint $table) {
            $table->integer('id_siswa')->primary();
            $table->string('nipd', 100);
            $table->string('password', 255);
            $table->string('nama', 120);
            $table->integer('id_jenis_kelamin');
            $table->string('nisn', 20);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('nik', 50);
            $table->integer('id_agama');
            $table->string('kebutuhan_khusus', 20);
            $table->text('alamat');
            $table->string('rt', 5);
            $table->string('rw', 5);
            $table->string('dusun', 100);
            $table->string('kelurahan', 100);
            $table->string('kecamatan', 100);
            $table->integer('kode_pos');
            $table->string('jenis_tinggal', 100);
            $table->string('alat_transportasi', 100);
            $table->string('telepon', 15);
            $table->string('hp', 15);
            $table->string('email', 150);
            $table->string('skhun', 50);
            $table->string('penerima_kps', 20);
            $table->string('no_kps', 50);
            $table->string('foto', 255);
            $table->string('nama_ayah', 150);
            $table->integer('tahun_lahir_ayah');
            $table->string('pendidikan_ayah', 50);
            $table->string('pekerjaan_ayah', 100);
            $table->string('penghasilan_ayah', 100);
            $table->string('kebutuhan_khusus_ayah', 100);
            $table->string('no_telpon_ayah', 15);
            $table->string('nama_ibu', 150);
            $table->integer('tahun_lahir_ibu');
            $table->string('pendidikan_ibu', 50);
            $table->string('pekerjaan_ibu', 100);
            $table->string('penghasilan_ibu', 100);
            $table->string('kebutuhan_khusus_ibu', 100);
            $table->string('no_telpon_ibu', 15);
            $table->string('nama_wali', 150);
            $table->integer('tahun_lahir_wali');
            $table->string('pendidikan_wali', 50);
            $table->string('pekerjaan_wali', 100);
            $table->string('penghasilan_wali', 50);
            $table->integer('angkatan');
            $table->string('status_awal', 20);
            $table->enum('status_siswa', ['Aktif', 'Tidak Aktif']);
            $table->string('tingkat', 10);
            $table->string('kode_kelas', 10);
            $table->string('kode_jurusan', 10);
            $table->integer('id_sesi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rb_siswa');
    }
};
