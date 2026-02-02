<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuis extends Model
{
    use HasFactory;

    protected $table = 'kuis';

    protected $fillable = [
        'kelas_id',
        'judul',
        'deskripsi',
        'durasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_soal',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    /* ================= RELATION ================= */

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function soal()
    {
        return $this->hasMany(KuisSoal::class);
    }

    public function attempts()
    {
        return $this->hasMany(KuisAttempt::class);
    }
}
