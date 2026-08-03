<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrangTua extends Model
{
    protected $table = 'orang_tuas';

    protected $fillable = ['user_id', 'nama_ayah', 'nama_ibu', 'no_hp'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function siswaAkses(): BelongsToMany
    {
        return $this->belongsToMany(
            Siswa::class,
            'akses_siswa_orang_tuas',
            'orangtua_id',
            'id_siswa',
            'id',
            'id_siswa'
        );
    }
}
