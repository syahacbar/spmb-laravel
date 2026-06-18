<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $path = (string) DB::table('tb_pengaturan_spmb')
            ->where('key', 'kepala_ttd_path')
            ->value('value');

        if (! str_starts_with($path, 'uploads/pengaturan/')) {
            return;
        }

        $basePath = realpath(public_path('uploads/pengaturan'));
        $filePath = realpath(public_path($path));

        if (! $basePath || ! $filePath || ! str_starts_with($filePath, $basePath.DIRECTORY_SEPARATOR) || ! is_file($filePath)) {
            return;
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $newPath = 'pengaturan/tanda-tangan/'.Str::uuid().($extension ? ".{$extension}" : '');

        if (! Storage::disk('local')->put($newPath, file_get_contents($filePath))) {
            throw new RuntimeException('Tanda tangan lama gagal dipindahkan ke storage private.');
        }

        DB::table('tb_pengaturan_spmb')
            ->where('key', 'kepala_ttd_path')
            ->update([
                'value' => $newPath,
                'updated_at' => now(),
            ]);

        unlink($filePath);
    }

    public function down(): void
    {
        // File tanda tangan tetap private saat rollback agar tidak kembali terekspos publik.
    }
};
