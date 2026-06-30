<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PengaturanSpmb extends Model
{
    private const SERVICE_TIMEZONE = 'Asia/Jayapura';

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
            'layanan_pendaftaran_aktif' => '1',
            'tanggal_buka_layanan_pendaftaran' => '2026-07-01',
            'tanggal_tutup_layanan_pendaftaran' => '2026-07-03',
            'jam_buka_layanan_pendaftaran' => '00:00',
            'jam_tutup_layanan_pendaftaran' => '23:59',
            'pesan_layanan_pendaftaran_tutup' => '',
        ];
    }

    public static function allSettings(): array
    {
        if (! Schema::hasTable((new self)->getTable())) {
            return self::defaults();
        }

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

    public static function registrationServiceIsOpen(?Carbon $now = null): bool
    {
        if (! Schema::hasTable((new self)->getTable())) {
            return true;
        }

        if (self::getValue('layanan_pendaftaran_aktif', '1') !== '1') {
            return true;
        }

        $now ??= Carbon::now(self::SERVICE_TIMEZONE);
        $now = $now->copy()->timezone(self::SERVICE_TIMEZONE);
        $openAt = self::serviceBoundary(
            (string) self::getValue('tanggal_buka_layanan_pendaftaran', '2026-07-01'),
            (string) self::getValue('jam_buka_layanan_pendaftaran', '00:00'),
            '2026-07-01',
            '00:00',
        );
        $closeAt = self::serviceBoundary(
            (string) self::getValue('tanggal_tutup_layanan_pendaftaran', '2026-07-03'),
            (string) self::getValue('jam_tutup_layanan_pendaftaran', '23:59'),
            '2026-07-03',
            '23:59',
        );

        return $now->betweenIncluded($openAt, $closeAt);
    }

    public static function registrationServiceMessage(): string
    {
        $customMessage = trim((string) self::getValue('pesan_layanan_pendaftaran_tutup', ''));

        if ($customMessage !== '') {
            return $customMessage;
        }

        $openDate = self::formatServiceDate((string) self::getValue('tanggal_buka_layanan_pendaftaran', '2026-07-01'), '2026-07-01');
        $closeDate = self::formatServiceDate((string) self::getValue('tanggal_tutup_layanan_pendaftaran', '2026-07-03'), '2026-07-03');
        $openTime = self::formatServiceTime((string) self::getValue('jam_buka_layanan_pendaftaran', '00:00'), '00:00');
        $closeTime = self::formatServiceTime((string) self::getValue('jam_tutup_layanan_pendaftaran', '23:59'), '23:59');

        if ($openTime === '00.00' && $closeTime === '23.59') {
            return "Layanan pendaftaran dibuka tanggal {$openDate} sampai {$closeDate} selama 24 jam WIT.";
        }

        return "Layanan pendaftaran dibuka tanggal {$openDate} pukul {$openTime} sampai tanggal {$closeDate} pukul {$closeTime} WIT.";
    }

    private static function serviceBoundary(string $date, string $time, string $dateFallback, string $timeFallback): Carbon
    {
        $date = self::normalizeServiceDate($date, $dateFallback);
        $time = self::normalizeServiceTime($time, $timeFallback);

        return Carbon::createFromFormat('Y-m-d H:i', "{$date} {$time}", self::SERVICE_TIMEZONE);
    }

    private static function formatServiceDate(string $date, string $fallback): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        $date = Carbon::parse(self::normalizeServiceDate($date, $fallback), self::SERVICE_TIMEZONE);

        return $date->day.' '.$months[$date->month].' '.$date->year;
    }

    private static function formatServiceTime(string $time, string $fallback): string
    {
        return str_replace(':', '.', self::normalizeServiceTime($time, $fallback));
    }

    private static function normalizeServiceTime(string $time, string $fallback): string
    {
        $time = trim($time);

        if (preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
            return $time;
        }

        return $fallback;
    }

    private static function normalizeServiceDate(string $date, string $fallback): string
    {
        $date = trim($date);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        return $fallback;
    }
}
