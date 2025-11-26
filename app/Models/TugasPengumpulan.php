<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasPengumpulan extends Model
{
    use HasFactory;

    protected $table = 'tugas_pengumpulans';

    protected $fillable = [
        'tugas_id',
        'siswa_nisn',       // ✅
        'jawaban',
        'file_path',
        'nilai',
        'catatan_guru',     // ✅
        'dinilai_oleh',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'dikumpul_pada' => 'datetime',
        'dinilai_pada' => 'datetime',
    ];

    // Relasi ke tugas
    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    // Relasi ke siswa
    public function siswa()
    {
        return $this->belongsTo(\App\Models\RbSiswa::class, 'siswa_nisn', 'nisn');  // ← PERBAIKI INI!
    }

    // Relasi ke guru yang menilai
    public function guru()
    {
        return $this->belongsTo(\App\Models\RbGuru::class, 'dinilai_oleh', 'nip');
    }
}
