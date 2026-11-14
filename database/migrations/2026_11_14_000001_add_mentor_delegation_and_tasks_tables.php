<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('intern_profiles', function (Blueprint $table) {
            $table->foreignId('mentor_id')
                ->nullable()
                ->after('supervisor_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::table('supervisor_profiles', function (Blueprint $table) {
            $table->foreignId('delegate_supervisor_id')
                ->nullable()
                ->after('phone')
                ->constrained('users')
                ->nullOnDelete();
            $table->date('delegation_start_date')->nullable()->after('delegate_supervisor_id');
            $table->date('delegation_end_date')->nullable()->after('delegation_start_date');
        });

        Schema::create('intern_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['todo', 'in_progress', 'submitted', 'approved', 'revision'])->default('todo');
            $table->text('submission_note')->nullable();
            $table->string('submission_file')->nullable();
            $table->text('reviewer_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('mentoring_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mentor_id')->constrained('users')->cascadeOnDelete();
            $table->date('session_date');
            $table->string('topic');
            $table->text('notes');
            $table->text('action_items')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentoring_notes');
        Schema::dropIfExists('intern_tasks');

        Schema::table('supervisor_profiles', function (Blueprint $table) {
            $table->dropForeign(['delegate_supervisor_id']);
            $table->dropColumn(['delegate_supervisor_id', 'delegation_start_date', 'delegation_end_date']);
        });

        Schema::table('intern_profiles', function (Blueprint $table) {
            $table->dropForeign(['mentor_id']);
            $table->dropColumn('mentor_id');
        });
    }
};
