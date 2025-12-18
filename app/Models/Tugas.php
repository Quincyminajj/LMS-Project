<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'kelas_id',
        'judul',
        'nilai_maksimal',
        'kkm',
        'deadline',
        'deskripsi',
        'file_contoh',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'nilai_maksimal' => 'decimal:2',
        'kkm' => 'decimal:2',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pengumpulan()
    {
        return $this->hasMany(TugasPengumpulan::class);
    }
}