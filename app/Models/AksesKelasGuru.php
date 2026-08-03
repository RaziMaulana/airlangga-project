<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AksesKelasGuru extends Model
{
    protected $table = 'akses_kelas_guru';

    protected $fillable = [
        'guru_id',
        'kelas_id',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function kelas()
    {
        return $this->belongsTo(kelas::class);
    }
}
