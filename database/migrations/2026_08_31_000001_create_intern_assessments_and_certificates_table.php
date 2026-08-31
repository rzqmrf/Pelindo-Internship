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
        Schema::create('intern_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('technical_skill')->default(80); // Penguasaan Tugas & Teknis (1-100)
            $table->unsignedInteger('discipline_score')->default(80); // Kedisiplinan & Ketepatan Waktu (1-100)
            $table->unsignedInteger('initiative_score')->default(80); // Inisiatif & Kreativitas (1-100)
            $table->unsignedInteger('teamwork_score')->default(80); // Komunikasi & Kerja Sama (1-100)
            $table->unsignedInteger('attitude_score')->default(80); // Sikap Kerja & Integritas (1-100)
            $table->decimal('final_score', 5, 2)->default(80.00); // Rata-rata akhir
            $table->string('predicate')->default('Baik'); // Sangat Baik (A), Baik (B), Cukup (C), Kurang (D)
            $table->text('notes_strengths')->nullable();
            $table->text('notes_improvements')->nullable();
            $table->text('supervisor_recommendation')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assessment_id')->nullable()->constrained('intern_assessments')->onDelete('set null');
            $table->string('certificate_number')->unique();
            $table->string('verification_token', 64)->unique();
            $table->date('issue_date');
            $table->enum('status', ['published', 'revoked'])->default('published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('intern_assessments');
    }
};
