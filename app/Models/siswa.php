<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrangTua;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Siswa extends Model
{
    protected $table = 'siswas';

    protected $primaryKey = 'id_siswa';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_siswa', 'user_id', 'uid_tag', 'nama_siswa', 'id_kelas'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orangTua(): BelongsToMany
    {
        return $this->belongsToMany(
            OrangTua::class,
            'akses_siswa_orang_tuas',
            'id_siswa',
            'orangtua_id',
            'id_siswa',
            'id'
        );
    }
}
