<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_pengguna', function (Blueprint $table): void {
            if (! Schema::hasColumn('tb_pengguna', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('level');
            }

            if (! Schema::hasColumn('tb_pengguna', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('is_verified');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_pengguna', function (Blueprint $table): void {
            if (Schema::hasColumn('tb_pengguna', 'verified_at')) {
                $table->dropColumn('verified_at');
            }

            if (Schema::hasColumn('tb_pengguna', 'is_verified')) {
                $table->dropColumn('is_verified');
            }
        });
    }
};
