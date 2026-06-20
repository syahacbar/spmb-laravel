<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use App\Models\PengaturanSpmb;
use App\Models\ProgramKeahlian;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);
        $formulirs = $this->filteredQuery($filters)
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.laporan', [
            'pengguna' => $request->attributes->get('pengguna'),
            'formulirs' => $formulirs,
            'filters' => $filters,
            'summary' => $this->summary($formulirs),
            'programSummary' => $this->programSummary($formulirs),
            'programAbbreviations' => $this->programAbbreviations(),
            'schoolSummary' => $this->schoolSummary($formulirs),
            'minatAOptions' => $this->programOptions('program_keahlian_1'),
            'minatBOptions' => $this->programOptions('program_keahlian_2'),
            'schoolOptions' => Formulir::query()
                ->whereNotNull('asal_sekolah')
                ->where('asal_sekolah', '!=', '')
                ->distinct()
                ->orderBy('asal_sekolah')
                ->pluck('asal_sekolah'),
            'reportHeadings' => $this->reportHeadings(),
            'reportRows' => $formulirs->values()->map(fn (Formulir $formulir, int $index): array => $this->reportRow($formulir, $index + 1)),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $formulirs = $this->filteredQuery($this->validatedFilters($request))
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->get();
        $filename = 'laporan-lengkap-spmb-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($formulirs): void {
            $output = fopen('php://output', 'w');

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $this->reportHeadings());

            foreach ($formulirs as $index => $formulir) {
                fputcsv($output, array_map(
                    fn (mixed $value): string => $this->csvValue($value),
                    $this->reportRow($formulir, $index + 1),
                ));
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function downloadBerkas(Request $request): BinaryFileResponse
    {
        $filters = $request->validate([
            'minat_a' => ['nullable', 'string', 'max:100'],
            'minat_b' => ['nullable', 'string', 'max:100'],
        ]);
        $formulirs = Formulir::query()
            ->where('status', 'submitted')
            ->when($filters['minat_a'] ?? null, fn (Builder $query, string $program) => $query->where('program_keahlian_1', $program))
            ->when($filters['minat_b'] ?? null, fn (Builder $query, string $program) => $query->where('program_keahlian_2', $program))
            ->orderBy('nama')
            ->get();

        abort_if($formulirs->isEmpty(), 404, 'Tidak ada berkas pendaftar yang sesuai dengan filter.');

        $temporaryPath = tempnam(sys_get_temp_dir(), 'spmb-berkas-');
        abort_unless($temporaryPath, 500, 'Gagal menyiapkan file unduhan.');

        $zip = new ZipArchive;
        abort_unless($zip->open($temporaryPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true, 500, 'Gagal membuat paket ZIP.');
        $fileCount = 0;
        $settings = PengaturanSpmb::allSettings();

        foreach ($formulirs as $formulir) {
            $nomorPendaftaran = $this->registrationNumber($formulir, $settings);
            $folder = $this->safeArchiveName($nomorPendaftaran.'-'.$formulir->nama);
            $cardHtml = view('formulir.kartu-arsip', [
                'formulir' => $formulir,
                'settings' => $settings,
                'nomorPendaftaran' => $nomorPendaftaran,
                'fotoDataUri' => $this->documentDataUri($formulir->foto_selfie),
            ])->render();

            $zip->addFromString($folder.'/kartu-pendaftaran.html', $cardHtml);
            $fileCount++;

            foreach (Formulir::DOCUMENT_FIELDS as $field) {
                $source = $this->documentPath($formulir->{$field});

                if (! $source) {
                    continue;
                }

                $label = match ($field) {
                    'surat_keterangan_lulus' => 'ijazah',
                    'kartu_keluarga' => 'kk',
                    'foto_selfie' => 'pas-foto',
                };
                $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
                $zip->addFile($source, $folder.'/'.$label.($extension ? '.'.$extension : ''));
                $fileCount++;
            }
        }

        $zip->close();

        if ($fileCount === 0) {
            @unlink($temporaryPath);
            abort(404, 'Tidak ada file berkas yang tersedia untuk diunduh.');
        }

        return response()
            ->download($temporaryPath, 'berkas-pendaftar-spmb-'.now()->format('Ymd-His').'.zip', [
                'Content-Type' => 'application/zip',
            ])
            ->deleteFileAfterSend(true);
    }

    private function validatedFilters(Request $request): array
    {
        $filters = $request->validate([
            'tanggal_pendaftaran' => ['nullable', 'date_format:d/m/Y'],
            'status' => ['nullable', 'in:draft,submitted'],
            'minat_a' => ['nullable', 'string', 'max:100'],
            'minat_b' => ['nullable', 'string', 'max:100'],
            'asal_sekolah' => ['nullable', 'string', 'max:150'],
        ], [
            'tanggal_pendaftaran.date_format' => 'Tanggal pendaftaran harus menggunakan format dd/mm/yyyy.',
        ]);

        if ($filters['tanggal_pendaftaran'] ?? null) {
            $filters['tanggal_pendaftaran_query'] = Carbon::createFromFormat('d/m/Y', $filters['tanggal_pendaftaran'])->format('Y-m-d');
        }

        return $filters;
    }

    private function filteredQuery(array $filters): Builder
    {
        return Formulir::query()
            ->when($filters['tanggal_pendaftaran_query'] ?? null, function (Builder $query, string $date): void {
                $query->whereDate(DB::raw('COALESCE(submitted_at, created_at)'), $date);
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['minat_a'] ?? null, fn (Builder $query, string $program) => $query->where('program_keahlian_1', $program))
            ->when($filters['minat_b'] ?? null, fn (Builder $query, string $program) => $query->where('program_keahlian_2', $program))
            ->when($filters['asal_sekolah'] ?? null, fn (Builder $query, string $school) => $query->where('asal_sekolah', $school));
    }

    private function summary(Collection $formulirs): array
    {
        return [
            'total' => $formulirs->count(),
            'submitted' => $formulirs->where('status', 'submitted')->count(),
            'draft' => $formulirs->where('status', 'draft')->count(),
            'laki_laki' => $formulirs->where('jenis_kelamin', 'Laki-laki')->count(),
            'perempuan' => $formulirs->where('jenis_kelamin', 'Perempuan')->count(),
        ];
    }

    private function programSummary(Collection $formulirs): Collection
    {
        $firstChoices = $formulirs->pluck('program_keahlian_1')->filter()->countBy();
        $secondChoices = $formulirs->pluck('program_keahlian_2')->filter()->countBy();

        return $firstChoices->keys()
            ->merge($secondChoices->keys())
            ->unique()
            ->map(fn (string $program): array => [
                'nama' => $program,
                'minat_a' => $firstChoices->get($program, 0),
                'minat_b' => $secondChoices->get($program, 0),
            ])
            ->sortBy('nama')
            ->values();
    }

    private function schoolSummary(Collection $formulirs): Collection
    {
        return $formulirs
            ->pluck('asal_sekolah')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->map(fn (int $total, string $school): array => [
                'asal_sekolah' => $school,
                'total' => $total,
            ])
            ->values();
    }

    private function programOptions(string $column): Collection
    {
        return Formulir::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column);
    }

    private function programAbbreviations(): array
    {
        return ProgramKeahlian::query()
            ->get(['nama', 'singkatan', 'aliases'])
            ->flatMap(function (ProgramKeahlian $program): array {
                $abbreviation = $program->singkatan ?: $this->programAbbreviation($program->nama);

                return collect([$program->nama, ...($program->aliases ?? [])])
                    ->mapWithKeys(fn (string $name): array => [$name => $abbreviation])
                    ->all();
            })
            ->all();
    }

    private function programAbbreviation(string $program): string
    {
        $name = Str::upper($program);

        return match (true) {
            Str::contains($name, ['AKUNTANSI', 'AKL']) => 'AKL',
            Str::contains($name, ['KOMPUTER', 'JARINGAN', 'TKJ']) => 'TKJ',
            Str::contains($name, ['KENDARAAN RINGAN', 'TKR']) => 'TKR',
            Str::contains($name, ['SEPEDA MOTOR', 'TSM']) => 'TSM',
            Str::contains($name, ['DESAIN KOMUNIKASI VISUAL', 'DKV']) => 'DKV',
            default => Str::of($program)
                ->split('/\s+/')
                ->filter()
                ->map(fn (string $word): string => Str::upper(Str::substr($word, 0, 1)))
                ->join(''),
        };
    }

    private function reportHeadings(): array
    {
        return [
            'No', 'NISN', 'NIK', 'Nama', 'Tempat Lahir', 'Tanggal Lahir', 'Jenis Kelamin', 'Agama',
            'No. HP Siswa', 'Asal Sekolah', 'Alamat Siswa', 'Nama Ayah', 'Pekerjaan Ayah',
            'Nama Ibu', 'Pekerjaan Ibu', 'No. HP Orang Tua', 'Alamat Orang Tua', 'Minat A',
            'Minat B', 'Status', 'Tanggal Pendaftaran',
        ];
    }

    private function reportRow(Formulir $formulir, int $number): array
    {
        $tanggal = $formulir->submitted_at ?: $formulir->created_at;

        return [
            $number,
            $formulir->nisn,
            $formulir->nik,
            $formulir->nama,
            $formulir->tempat_lahir,
            $formulir->tanggal_lahir?->format('d/m/Y'),
            $formulir->jenis_kelamin,
            $formulir->agama,
            $formulir->hp,
            $formulir->asal_sekolah,
            $this->studentAddress($formulir),
            $formulir->nama_ayah,
            $formulir->pekerjaan_ayah,
            $formulir->nama_ibu,
            $formulir->pekerjaan_ibu,
            $formulir->hp_ortu,
            $this->parentAddress($formulir),
            $formulir->program_keahlian_1,
            $formulir->program_keahlian_2,
            $formulir->status === 'submitted' ? 'Final' : 'Draf',
            $tanggal?->format('d/m/Y H:i'),
        ];
    }

    private function studentAddress(Formulir $formulir): string
    {
        return collect([
            $formulir->alamat,
            $formulir->alamat_kelurahan ? 'Kel. '.$formulir->alamat_kelurahan : null,
            $formulir->alamat_kecamatan ? 'Kec. '.$formulir->alamat_kecamatan : null,
            $formulir->alamat_kabupaten ? 'Kab. '.$formulir->alamat_kabupaten : null,
        ])->filter()->join(', ');
    }

    private function parentAddress(Formulir $formulir): string
    {
        return collect([
            $formulir->alamat_ortu,
            $formulir->alamat_ortu_kelurahan ? 'Kel. '.$formulir->alamat_ortu_kelurahan : null,
            $formulir->alamat_ortu_kecamatan ? 'Kec. '.$formulir->alamat_ortu_kecamatan : null,
            $formulir->alamat_ortu_kabupaten ? 'Kab. '.$formulir->alamat_ortu_kabupaten : null,
        ])->filter()->join(', ');
    }

    private function documentPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'dokumen/') && Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->path($path);
        }

        if (! str_starts_with($path, 'uploads/dokumen/')) {
            return null;
        }

        $basePath = realpath(public_path('uploads/dokumen'));
        $filePath = realpath(public_path($path));

        return $basePath && $filePath && str_starts_with($filePath, $basePath.DIRECTORY_SEPARATOR) && is_file($filePath)
            ? $filePath
            : null;
    }

    private function documentDataUri(?string $path): ?string
    {
        $source = $this->documentPath($path);

        if (! $source) {
            return null;
        }

        $mime = mime_content_type($source) ?: 'application/octet-stream';

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($source));
    }

    private function registrationNumber(Formulir $formulir, array $settings): string
    {
        $year = $settings['tahun_pendaftaran'] ?? now()->format('Y');

        return 'SPMB-'.$year.'-'.str_pad((string) $formulir->id, 3, '0', STR_PAD_LEFT);
    }

    private function safeArchiveName(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->replaceMatches('/[^A-Za-z0-9._-]+/', '-')
            ->trim('-')
            ->limit(100, '')
            ->value();
    }

    private function csvValue(mixed $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@]/', $value) ? "'".$value : $value;
    }
}
