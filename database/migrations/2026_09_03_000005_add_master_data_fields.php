<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('intern_profiles')) {
            Schema::table('intern_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('intern_profiles', 'religion')) {
                    $table->string('religion')->nullable()->after('major');
                }
                if (!Schema::hasColumn('intern_profiles', 'gender')) {
                    $table->string('gender')->nullable()->after('religion');
                }
                if (!Schema::hasColumn('intern_profiles', 'address')) {
                    $table->text('address')->nullable()->after('gender');
                }
            });
        }

        if (Schema::hasTable('supervisor_profiles')) {
            Schema::table('supervisor_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('supervisor_profiles', 'religion')) {
                    $table->string('religion')->nullable()->after('position_title');
                }
                if (!Schema::hasColumn('supervisor_profiles', 'gender')) {
                    $table->string('gender')->nullable()->after('religion');
                }
                if (!Schema::hasColumn('supervisor_profiles', 'address')) {
                    $table->text('address')->nullable()->after('gender');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('intern_profiles')) {
            Schema::table('intern_profiles', function (Blueprint $table) {
                $table->dropColumn(['religion', 'gender', 'address']);
            });
        }

        if (Schema::hasTable('supervisor_profiles')) {
            Schema::table('supervisor_profiles', function (Blueprint $table) {
                $table->dropColumn(['religion', 'gender', 'address']);
            });
        }
    }
};
