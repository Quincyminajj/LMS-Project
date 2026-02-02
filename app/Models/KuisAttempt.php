<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KuisAttempt extends Model
{
    use HasFactory;

    protected $table = 'kuis_attempts';

    protected $fillable = [
        'kuis_id',
        'siswa_nisn',
        'mulai_pada',
        'selesai_pada',
        'durasi',
        'nilai_akhir',
    ];

    protected $casts = [
        'mulai_pada' => 'datetime',
        'selesai_pada' => 'datetime',
        'nilai_akhir' => 'decimal:2',
    ];

    /**
     * Relasi ke Kuis
     */
    public function kuis()
    {
        return $this->belongsTo(Kuis::class, 'kuis_id');
    }

    /**
     * Relasi ke Siswa
     * PENTING: Pastikan foreign key dan local key benar
     * Format: belongsTo(Model, 'foreign_key', 'owner_key')
     */
    public function siswa()
    {
        // siswa_nisn = kolom di tabel kuis_attempts
        // nisn = kolom primary key di tabel siswa
        return $this->belongsTo(RbSiswa::class, 'siswa_nisn', 'nisn');
    }
}