<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $invalidNiks = DB::table('tb_formulir')
            ->whereRaw("nik NOT REGEXP '^[0-9]{16}$'")
            ->pluck('nik');

        if ($invalidNiks->isNotEmpty()) {
            throw new RuntimeException(
                'Validasi NIK database tidak dapat dipasang karena ditemukan NIK yang bukan 16 digit: '
                .$invalidNiks->implode(', '),
            );
        }

        $duplicateNiks = DB::table('tb_formulir')
            ->select('nik')
            ->groupBy('nik')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('nik');

        if ($duplicateNiks->isNotEmpty()) {
            throw new RuntimeException(
                'Unique constraint tb_formulir.nik tidak dapat dipasang karena ditemukan NIK ganda: '
                .$duplicateNiks->implode(', '),
            );
        }

        Schema::table('tb_formulir', function (Blueprint $table): void {
            $table->string('nik', 16)->change();
            $table->unique('nik', 'tb_formulir_nik_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tb_formulir', function (Blueprint $table): void {
            $table->dropUnique('tb_formulir_nik_unique');
            $table->string('nik', 30)->change();
        });
    }
};
