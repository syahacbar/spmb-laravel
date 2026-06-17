<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use SplFileObject;

class WilayahIndonesiaSeeder extends Seeder
{
    private const CSV_PATH = 'seeders/data/tbl_wilayahs.csv';

    public function run(): void
    {
        DB::disableQueryLog();

        $levels = $this->readCsv();

        DB::transaction(function () use ($levels): void {
            $this->importProvinsi($levels[0]);
            $provinsiIds = $this->codeMap('ref_wilayah_provinsi');

            $this->importKabupaten($levels[1], $provinsiIds);
            $kabupatenIds = $this->codeMap('ref_wilayah_kabupaten');

            $this->importKecamatan($levels[2], $kabupatenIds);
            $kecamatanIds = $this->codeMap('ref_wilayah_kecamatan');

            $this->importKelurahan($levels[3], $kecamatanIds);
            $this->deleteLegacyRowsWithoutCode();
        });
    }

    private function readCsv(): array
    {
        $path = database_path(self::CSV_PATH);

        if (! is_file($path)) {
            throw new RuntimeException("File CSV wilayah tidak ditemukan: {$path}");
        }

        $file = new SplFileObject($path);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);
        $file->setCsvControl(',', '"', '\\');

        $levels = [[], [], [], []];
        $orders = [[], [], [], []];
        $isHeader = true;

        foreach ($file as $row) {
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            if (! is_array($row) || count($row) < 2 || ! isset($row[0], $row[1])) {
                continue;
            }

            $code = trim((string) $row[0]);
            $name = trim((string) $row[1]);

            if ($code === '' || $name === '') {
                continue;
            }

            $level = substr_count($code, '.');

            if ($level < 0 || $level > 3) {
                continue;
            }

            $parent = $level === 0 ? 'root' : $this->parentCode($code);
            $orders[$level][$parent] = ($orders[$level][$parent] ?? 0) + 1;

            $levels[$level][$code] = [
                'kode' => $code,
                'nama' => $name,
                'urutan' => $orders[$level][$parent],
                'created_at' => $row[2] ?? now(),
                'updated_at' => $row[3] ?? now(),
            ];
        }

        return $levels;
    }

    private function importProvinsi(array $rows): void
    {
        $this->upsertChunks('ref_wilayah_provinsi', array_values($rows), ['nama'], [
            'kode',
            'urutan',
            'updated_at',
        ]);
    }

    private function importKabupaten(array $rows, array $provinsiIds): void
    {
        $payload = [];

        foreach ($rows as $row) {
            $provinsiId = $provinsiIds[$this->parentCode($row['kode'])] ?? null;

            if (! $provinsiId) {
                continue;
            }

            $payload[] = array_merge($row, ['provinsi_id' => $provinsiId]);
        }

        $this->upsertChunks('ref_wilayah_kabupaten', $payload, ['provinsi_id', 'nama'], [
            'kode',
            'urutan',
            'updated_at',
        ]);
    }

    private function importKecamatan(array $rows, array $kabupatenIds): void
    {
        $payload = [];

        foreach ($rows as $row) {
            $kabupatenId = $kabupatenIds[$this->parentCode($row['kode'])] ?? null;

            if (! $kabupatenId) {
                continue;
            }

            $payload[] = array_merge($row, ['kabupaten_id' => $kabupatenId]);
        }

        $this->upsertChunks('ref_wilayah_kecamatan', $payload, ['kabupaten_id', 'nama'], [
            'kode',
            'urutan',
            'updated_at',
        ]);
    }

    private function importKelurahan(array $rows, array $kecamatanIds): void
    {
        $payload = [];

        foreach ($rows as $row) {
            $kecamatanId = $kecamatanIds[$this->parentCode($row['kode'])] ?? null;

            if (! $kecamatanId) {
                continue;
            }

            $payload[] = array_merge($row, ['kecamatan_id' => $kecamatanId]);
        }

        $this->upsertChunks('ref_wilayah_kelurahan', $payload, ['kecamatan_id', 'nama'], [
            'kode',
            'urutan',
            'updated_at',
        ]);
    }

    private function codeMap(string $table): array
    {
        return DB::table($table)
            ->whereNotNull('kode')
            ->pluck('id', 'kode')
            ->all();
    }

    private function upsertChunks(string $table, array $rows, array $uniqueBy, array $updateColumns): void
    {
        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table($table)->upsert($chunk, $uniqueBy, $updateColumns);
        }
    }

    private function deleteLegacyRowsWithoutCode(): void
    {
        foreach ([
            'ref_wilayah_kelurahan',
            'ref_wilayah_kecamatan',
            'ref_wilayah_kabupaten',
            'ref_wilayah_provinsi',
        ] as $table) {
            DB::table($table)->whereNull('kode')->delete();
        }
    }

    private function parentCode(string $code): string
    {
        return substr($code, 0, strrpos($code, '.'));
    }
}
