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
        Schema::create('intern_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('set null');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('student_id_number')->nullable(); // NIM / NIS
            $table->string('nik_number')->nullable(); // No. KTP / NIK
            $table->string('institution')->nullable(); // Universitas / Sekolah
            $table->string('major')->nullable(); // Jurusan
            $table->string('bank_name')->nullable(); // Nama Bank (Mandiri, BRI, BNI, BCA, dll)
            $table->string('bank_account_number')->nullable(); // No. Rekening
            $table->string('bank_account_holder')->nullable(); // Atas Nama Rekening
            $table->text('address')->nullable(); // Alamat
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'completed', 'extended', 'terminated'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_profiles');
    }
};
