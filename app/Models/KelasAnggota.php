<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasAnggota extends Model
{
    use HasFactory;

    protected $table = 'kelas_anggotas';
    public $timestamps = false;

    protected $fillable = [
        'kelas_id',
        'siswa_nisn',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    // Relasi ke kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke siswa
    public function siswa()
    {
        return $this->belongsTo(RbSiswa::class, 'siswa_nisn', 'nisn');
    }
}
