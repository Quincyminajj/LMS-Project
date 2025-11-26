<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumKomentar extends Model
{
    protected $table = 'forum_komentars';

    // ✅ Timestamps AKTIF (default Laravel)
    public $timestamps = true;

    protected $fillable = [
        'forum_id',
        'isi',
        'dibuat_oleh',
        'pengirim_nisn_nip',
        'parent_id'
    ];

    // Relasi
    public function forum()
    {
        return $this->belongsTo(Forum::class, 'forum_id');
    }

    public function parent()
    {
        return $this->belongsTo(ForumKomentar::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumKomentar::class, 'parent_id');
    }
}
