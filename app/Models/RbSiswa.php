<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class RbSiswa extends Authenticatable
{
    use HasFactory;

    protected $table = 'rb_siswa';
    protected $primaryKey = 'nisn';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_siswa',
        'nipd',
        'password',
        'nama',
        'nisn',
        'email',
        'hp',
        'foto',
        'status_siswa',
        'angkatan',
    ];

    protected $hidden = [
        'password',
    ];

    // Relasi ke kelas yang diikuti
    public function kelasAnggota()
    {
        return $this->hasMany(KelasAnggota::class, 'siswa_nisn', 'nisn');
    }

    // Relasi ke kelas melalui pivot
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_anggotas', 'siswa_nisn', 'kelas_id')
            ->withPivot('joined_at');
    }

    // Relasi ke pengumpulan tugas
    public function tugasPengumpulan()
    {
        return $this->hasMany(TugasPengumpulan::class, 'siswa_nisn', 'nisn');
    }
}
