<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengguna extends Model
{
    protected $table = 'tb_pengguna';

    protected $primaryKey = 'id_pengguna';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = [
        'id_pengguna',
        'nama_pengguna',
        'alamat',
        'telpon',
        'email',
        'username',
        'password',
        'level',
        'is_verified',
        'is_active',
        'verified_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'id_pengguna', 'nisn');
    }

    public function formulirTerbaru(): HasOne
    {
        return $this->formulir();
    }

    public function formulir(): HasOne
    {
        return $this->hasOne(Formulir::class, 'nisn', 'id_pengguna');
    }

    public function whatsappPhone(): string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->telpon) ?? '';

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }

    public function verificationWhatsappMessage(): string
    {
        $nama = trim((string) ($this->calonSiswa?->nama ?: $this->nama_pengguna));

        if ($nama === '') {
            $nama = 'Calon Siswa';
        }

        return implode("\n", [
            "Halo, {$nama}",
            '',
            "Akun SPMB Anda dengan NISN {$this->id_pengguna} telah aktif.",
            'Silahkan login di spmb.smkn1bintuni.sch.id/login untuk mengisi biodata dan melengkapi berkas persyaratan.',
            '',
            'Panitia SPMB SMK Negeri 1 Bintuni',
        ]);
    }

    public function verificationWhatsappUrl(): string
    {
        return 'https://wa.me/'.$this->whatsappPhone().'?text='.rawurlencode($this->verificationWhatsappMessage());
    }
}
