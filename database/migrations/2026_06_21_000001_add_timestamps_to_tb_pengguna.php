<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_pengguna', function (Blueprint $table): void {
            if (! Schema::hasColumn('tb_pengguna', 'created_at')) {
                $table->timestamp('created_at')->useCurrent();
            }
            if (! Schema::hasColumn('tb_pengguna', 'updated_at')) {
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_pengguna', function (Blueprint $table): void {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }
};
