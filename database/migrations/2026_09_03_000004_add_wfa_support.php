<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('allowance_claims') && !Schema::hasColumn('allowance_claims', 'wfa_count')) {
            Schema::table('allowance_claims', function (Blueprint $table) {
                $table->unsignedInteger('wfa_count')->default(0)->after('wfh_count');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('allowance_claims') && Schema::hasColumn('allowance_claims', 'wfa_count')) {
            Schema::table('allowance_claims', function (Blueprint $table) {
                $table->dropColumn('wfa_count');
            });
        }
    }
};
