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
        Schema::create('allowance_periods', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g. "Klaim Uang Saku September 2026"
            $table->date('start_date'); // Tanggal awal logbook/presensi
            $table->date('end_date'); // Tanggal akhir logbook/presensi
            $table->date('submission_start_date'); // Tanggal HR BUKA submit
            $table->date('submission_end_date'); // Tanggal HR TUTUP submit
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowance_periods');
    }
};
