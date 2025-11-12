<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $fillable = [
        'kode_kelas',
        'nama_kelas',
        'guru_nip',
        'deskripsi',
        'status',
    ];

    public function anggota()
    {
        return $this->hasMany(KelasAnggota::class, 'kelas_id');
    }

    public function konten()
    {
        return $this->hasMany(KelasKonten::class, 'kelas_id');
    }

    public function guru()
    {
        return $this->belongsTo(RbGuru::class, 'guru_nip', 'nip');
    }
}
