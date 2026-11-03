<?php

use App\Models\Role;
use App\Models\SystemSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)
                ->default('intern')
                ->index()
                ->change();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->string('base_portal', 30)->default('admin');
            $table->string('badge_color', 30)->default('blue');
            $table->json('permissions')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Role::ensureDefaults();

        if (Schema::hasTable('system_settings')) {
            SystemSetting::firstOrCreate(
                ['key' => 'maintenance_bypass_token'],
                ['value' => Str::random(32), 'type' => 'string']
            );
            SystemSetting::flushCache();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
        Role::flushCache();

        DB::table('users')
            ->whereNotIn('role', ['superadmin', 'admin', 'supervisor', 'intern'])
            ->update(['role' => 'admin']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['superadmin', 'admin', 'supervisor', 'intern'])
                ->default('intern')
                ->change();
        });
    }
};
