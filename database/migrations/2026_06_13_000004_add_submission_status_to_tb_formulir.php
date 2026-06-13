<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_formulir', function (Blueprint $table): void {
            if (! Schema::hasColumn('tb_formulir', 'status')) {
                $table->string('status', 20)->default('draft')->after('foto_selfie');
            }

            if (! Schema::hasColumn('tb_formulir', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('status');
            }
        });

        DB::table('tb_formulir')
            ->where('status', 'draft')
            ->update([
                'status' => 'submitted',
                'submitted_at' => DB::raw('created_at'),
            ]);
    }

    public function down(): void
    {
        Schema::table('tb_formulir', function (Blueprint $table): void {
            if (Schema::hasColumn('tb_formulir', 'submitted_at')) {
                $table->dropColumn('submitted_at');
            }

            if (Schema::hasColumn('tb_formulir', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
