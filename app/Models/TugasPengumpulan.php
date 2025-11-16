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
        'siswa_nisn',
        'tipe',
        'isi',
        'file_path',
        'nilai',
        'feedback',
        'dinilai_oleh',
        'dikumpul_pada',
        'dinilai_pada',
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
}
