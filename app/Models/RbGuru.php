<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class RbGuru extends Authenticatable
{
    use HasFactory;

    protected $table = 'rb_guru';
    protected $primaryKey = 'nip';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nip',
        'password',
        'nama_guru',
        'email',
        'hp',
        'foto',
    ];

    protected $hidden = [
        'password',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'guru_nip', 'nip');
    }
}
