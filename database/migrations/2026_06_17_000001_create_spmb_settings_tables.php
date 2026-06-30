<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tb_pengaturan_spmb')) {
            Schema::create('tb_pengaturan_spmb', function (Blueprint $table): void {
                $table->string('key')->primary();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tb_program_keahlian')) {
            Schema::create('tb_program_keahlian', function (Blueprint $table): void {
                $table->id();
                $table->string('nama', 100)->unique();
                $table->string('singkatan', 20)->nullable();
                $table->unsignedInteger('kuota')->default(0);
                $table->json('aliases')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('urutan')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tb_kontak_panitia')) {
            Schema::create('tb_kontak_panitia', function (Blueprint $table): void {
                $table->id();
                $table->string('nama', 100);
                $table->string('label', 100)->nullable();
                $table->string('nomor_whatsapp', 20);
                $table->boolean('is_primary')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        $now = now();

        foreach ($this->defaultSettings() as $key => $value) {
            DB::table('tb_pengaturan_spmb')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => $now, 'created_at' => $now],
            );
        }

        foreach ($this->defaultPrograms() as $program) {
            DB::table('tb_program_keahlian')->updateOrInsert(
                ['nama' => $program['nama']],
                array_merge($program, ['updated_at' => $now, 'created_at' => $now]),
            );
        }

        if (DB::table('tb_kontak_panitia')->count() === 0) {
            DB::table('tb_kontak_panitia')->insert([
                'nama' => 'Petugas SPMB',
                'label' => 'Admin Pendaftaran',
                'nomor_whatsapp' => '6281111110002',
                'is_primary' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_kontak_panitia');
        Schema::dropIfExists('tb_program_keahlian');
        Schema::dropIfExists('tb_pengaturan_spmb');
    }

    private function defaultSettings(): array
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

    private function defaultPrograms(): array
    {
        return [
            [
                'nama' => 'Akuntansi dan Keuangan Lembaga (AKL)',
                'singkatan' => 'AKL',
                'kuota' => 72,
                'aliases' => json_encode(['Akuntansi dan Keuangan Lembaga']),
                'is_active' => true,
                'urutan' => 1,
            ],
            [
                'nama' => 'Teknik Kendaraan Ringan (TKR)',
                'singkatan' => 'TKR',
                'kuota' => 36,
                'aliases' => json_encode(['Teknik Kendaraan Ringan']),
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'nama' => 'Teknik Komputer dan Jaringan (TKJ)',
                'singkatan' => 'TKJ',
                'kuota' => 36,
                'aliases' => json_encode(['Teknik Komputer dan Jaringan', 'Teknik Jaringan dan Telekomunikasi']),
                'is_active' => true,
                'urutan' => 3,
            ],
            [
                'nama' => 'Desain Komunikasi Visual (DKV)',
                'singkatan' => 'DKV',
                'kuota' => 36,
                'aliases' => json_encode(['Desain Komunikasi Visual']),
                'is_active' => true,
                'urutan' => 4,
            ],
            [
                'nama' => 'Teknik Sepeda Motor (TSM)',
                'singkatan' => 'TSM',
                'kuota' => 36,
                'aliases' => json_encode(['Teknik Sepeda Motor']),
                'is_active' => true,
                'urutan' => 5,
            ],
        ];
    }
};
