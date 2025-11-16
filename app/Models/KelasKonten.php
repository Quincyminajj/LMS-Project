<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasKonten extends Model
{
    use HasFactory;

    protected $table = 'kelas_kontens';

    protected $fillable = [
        'kelas_id',
        'judul',
        'tipe',
        'isi',
        'file_path',
        'uploaded_by',
    ];

    public function kelas()
    {
        return $this->hasMany(KelasKonten::class);
    }
}
