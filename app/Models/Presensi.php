<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $fillable = ['uid_tag', 'id_kelas', 'waktu', 'status', 'tanggal'];

    public function siswa()
    {
        return $this->belongsTo(siswa::class, 'uid_tag', 'uid_tag');
    }

    public function kelas()
    {
        return $this->belongsTo(kelas::class, 'id_kelas');
    }
}
