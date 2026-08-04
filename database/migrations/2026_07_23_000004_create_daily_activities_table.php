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
        Schema::create('daily_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('date');
            $table->enum('work_location', ['WFO', 'WFH', 'Leave'])->default('WFO');
            $table->text('activity_description');
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'revision', 'rejected'])->default('pending');
            $table->text('supervisor_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_activities');
    }
};
