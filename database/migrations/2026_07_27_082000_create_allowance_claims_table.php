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
        Schema::create('allowance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('period_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('wfo_count')->default(0);
            $table->integer('wfh_count')->default(0);
            $table->integer('leave_count')->default(0);
            $table->decimal('total_allowance', 15, 2)->default(0);
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->string('attachment_path')->nullable(); // PDF logbook
            $table->enum('status', ['pending_supervisor', 'approved_by_supervisor', 'processed_by_hr', 'rejected'])->default('pending_supervisor');
            $table->text('supervisor_note')->nullable();
            $table->text('hr_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowance_claims');
    }
};
