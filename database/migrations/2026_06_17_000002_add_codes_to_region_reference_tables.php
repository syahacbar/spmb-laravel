<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'ref_wilayah_provinsi',
            'ref_wilayah_kabupaten',
            'ref_wilayah_kecamatan',
            'ref_wilayah_kelurahan',
        ] as $tableName) {
            if (! Schema::hasColumn($tableName, 'kode')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->string('kode', 20)->nullable()->unique()->after('id');
                });
            }
        }
    }

    public function down(): void
    {
        foreach ([
            'ref_wilayah_kelurahan',
            'ref_wilayah_kecamatan',
            'ref_wilayah_kabupaten',
            'ref_wilayah_provinsi',
        ] as $tableName) {
            if (Schema::hasColumn($tableName, 'kode')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                    $table->dropUnique($tableName.'_kode_unique');
                    $table->dropColumn('kode');
                });
            }
        }
    }
};
