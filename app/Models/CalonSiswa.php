<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalonSiswa extends Model
{
    protected $table = 'tb_calon_siswa';

    protected $primaryKey = 'nisn';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'nisn',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'asal_sekolah',
        'tahun_pendaftaran',
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_active' => 'boolean',
    ];

    public function scopeActiveForYear($query, string $year)
    {
        return $query->where('tahun_pendaftaran', $year)->where('is_active', true);
    }
}
