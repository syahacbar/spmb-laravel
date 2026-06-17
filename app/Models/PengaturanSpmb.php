<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PengaturanSpmb extends Model
{
    protected $table = 'tb_pengaturan_spmb';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public static function defaults(): array
    {
        return [
            'tahun_pendaftaran' => '2026',
            'tahun_pelajaran' => '2026/2027',
            'kepala_nama' => 'Panitia SPMB',
            'kepala_nip' => '',
            'kepala_jabatan' => 'Panitia SPMB',
            'kepala_ttd_path' => 'images/ttdketua.png',
            'tanggal_tes' => '06 Juli 2026',
            'waktu_tes' => '08.00 WIT s.d. selesai',
            'tempat_tes' => 'SMK Negeri 1 Bintuni',
            'catatan_kartu' => "Peserta wajib mengikuti tahap wawancara dan pemetaan jurusan sesuai jadwal yang tercantum pada kartu ini.\nPeserta wajib mencetak dan membawa kartu pendaftaran sebagai bukti keikutsertaan.\nPeserta wajib mengenakan seragam SMP lengkap dan berpenampilan rapi.",
        ];
    }

    public static function allSettings(): array
    {
        return array_replace(
            self::defaults(),
            self::query()->pluck('value', 'key')->all(),
        );
    }

    public static function getValue(string $key, ?string $fallback = null): ?string
    {
        $settings = self::allSettings();

        return $settings[$key] ?? $fallback;
    }

    public static function setMany(array $settings): void
    {
        DB::transaction(function () use ($settings): void {
            foreach ($settings as $key => $value) {
                self::query()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value],
                );
            }
        });
    }
}
