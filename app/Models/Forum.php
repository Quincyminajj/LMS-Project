<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;

    protected $table = 'forums';

    protected $fillable = [
        'kelas_id',
        'judul',
        'isi',
        'dibuat_oleh',
        'gambar', // Tambahkan ini
    ];

    // Relasi ke kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    // Relasi ke komentar
    public function komentars()
    {
        return $this->hasMany(ForumKomentar::class);
    }
}