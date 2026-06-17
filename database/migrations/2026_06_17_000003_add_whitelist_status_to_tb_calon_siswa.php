<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_calon_siswa', function (Blueprint $table): void {
            if (! Schema::hasColumn('tb_calon_siswa', 'tahun_pendaftaran')) {
                $table->string('tahun_pendaftaran', 4)->default('2026')->after('asal_sekolah');
            }

            if (! Schema::hasColumn('tb_calon_siswa', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('tahun_pendaftaran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_calon_siswa', function (Blueprint $table): void {
            if (Schema::hasColumn('tb_calon_siswa', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('tb_calon_siswa', 'tahun_pendaftaran')) {
                $table->dropColumn('tahun_pendaftaran');
            }
        });
    }
};
