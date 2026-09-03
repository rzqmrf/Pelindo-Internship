<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('intern_assessments', function (Blueprint $table) {
            $table->string('final_project_title')->nullable()->after('supervisor_recommendation');
            $table->string('final_project_file')->nullable()->after('final_project_title');
            $table->string('final_project_link')->nullable()->after('final_project_file');
            $table->timestamp('submitted_at')->nullable()->after('final_project_link');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('check_in_method', ['geofence', 'qr_code'])->default('geofence')->after('work_location');
            $table->string('qr_token_verified')->nullable()->after('check_in_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intern_assessments', function (Blueprint $table) {
            $table->dropColumn(['final_project_title', 'final_project_file', 'final_project_link', 'submitted_at']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['check_in_method', 'qr_token_verified']);
        });
    }
};
