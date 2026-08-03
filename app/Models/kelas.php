<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
    ];

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id');
    }

    /**
     * Data akses guru terhadap kelas.
     */
    public function aksesGuru(): HasMany
    {
        return $this->hasMany(AksesKelasGuru::class, 'kelas_id', 'id');
    }

    /**
     * Daftar guru yang memiliki akses ke kelas ini.
     */
    public function guru(): BelongsToMany
    {
        return $this->belongsToMany(
            Guru::class,
            'akses_kelas_gurus',
            'kelas_id',
            'guru_id'
        );
    }
}
