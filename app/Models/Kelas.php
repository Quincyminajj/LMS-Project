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

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'kelas_id');
    }

    public function tugasPengumpulan()
    {
        return $this->hasMany(TugasPengumpulan::class, 'kelas_id');
    }

    public function forums()
    {
        return $this->hasMany(Forum::class, 'kelas_id');
    }
}
