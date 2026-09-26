<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('category')->nullable();
            $table->string('type')->default('6_months_diploma'); // short_term, 3_months, 6_months_diploma, 1_year_diploma, advanced_diploma
            $table->unsignedInteger('duration_weeks')->default(24);
            $table->decimal('total_fee', 10, 2);
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('eligibility')->nullable();
            $table->string('certificate_title')->nullable();
            $table->string('status')->default('active'); // active, archived
            $table->timestamps();

            $table->unique(['franchise_id', 'code']);
        });

        // 2. Course Modules (Syllabus)
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(1);
            $table->unsignedInteger('duration_hours')->default(10);
            $table->timestamps();
        });

        // 3. Batches
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('batch_code');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('time_slot'); // e.g. "10:00 AM - 12:00 PM"
            $table->unsignedInteger('capacity')->default(30);
            $table->string('status')->default('active'); // upcoming, active, completed, cancelled
            $table->timestamps();

            $table->unique(['branch_id', 'batch_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('courses');
    }
};
