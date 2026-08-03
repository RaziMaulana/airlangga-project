<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    protected $table = 'gurus';

    protected $fillable = [
        'user_id',
        'nama_guru',
        'nip',
    ];

    /**
     * Data akses kelas milik guru.
     */
    public function aksesKelas(): HasMany
    {
        return $this->hasMany(AksesKelasGuru::class, 'guru_id', 'id');
    }

    /**
     * Daftar kelas yang dapat diakses guru.
     */
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(
            Kelas::class,
            'akses_kelas_gurus',
            'guru_id',
            'kelas_id'
        );
    }
}
