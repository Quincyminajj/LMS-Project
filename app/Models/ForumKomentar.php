<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumKomentar extends Model
{
    use HasFactory;

    protected $table = 'forum_komentars';

    public $timestamps = false; // Karena created_at sudah di-handle manual

    protected $fillable = [
        'forum_id',
        'pengirim_nisn_nip',
        'pengirim_tipe',
        'isi',
        'parent_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
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
