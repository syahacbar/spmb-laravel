<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramKeahlian extends Model
{
    protected $table = 'tb_program_keahlian';

    protected $fillable = [
        'nama',
        'singkatan',
        'kuota',
        'aliases',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'aliases' => 'array',
        'is_active' => 'boolean',
        'kuota' => 'integer',
        'urutan' => 'integer',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('nama');
    }
}
