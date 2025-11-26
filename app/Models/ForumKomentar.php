<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumKomentar extends Model
{
    use HasFactory;

    protected $table = 'forum_komentars';

    public $timestamps = true; // Aktifkan timestamps untuk created_at dan updated_at

    protected $fillable = [
        'forum_id',
        'pengirim_nisn_nip',
        'pengirim_tipe',
        'isi',
        'dibuat_oleh', // Tambahkan field ini untuk menyimpan nama user
        'parent_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke forum
    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

    // Relasi ke parent komentar (jika reply)
    public function parent()
    {
        return $this->belongsTo(ForumKomentar::class, 'parent_id');
    }

    // Relasi ke child komentar (reply)
    public function children()
    {
        return $this->hasMany(ForumKomentar::class, 'parent_id');
    }
}
