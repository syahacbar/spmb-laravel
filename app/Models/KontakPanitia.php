<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Throwable;

class KontakPanitia extends Model
{
    protected $table = 'tb_kontak_panitia';

    protected $fillable = [
        'nama',
        'label',
        'nomor_whatsapp',
        'is_primary',
        'is_active',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function primary(): ?self
    {
        try {
            return self::query()
                ->active()
                ->orderByDesc('is_primary')
                ->orderBy('id')
                ->first();
        } catch (Throwable) {
            return null;
        }
    }

    public function whatsappPhone(): string
    {
        return preg_replace('/\D+/', '', $this->nomor_whatsapp);
    }
}
