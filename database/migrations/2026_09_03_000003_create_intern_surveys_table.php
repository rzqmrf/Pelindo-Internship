<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intern_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('satisfaction_rating')->default(5);
            $table->unsignedTinyInteger('supervisor_rating')->default(5);
            $table->unsignedTinyInteger('facility_rating')->default(5);
            $table->text('feedback_notes')->nullable();
            $table->unsignedTinyInteger('recommend_score')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intern_surveys');
    }
};
