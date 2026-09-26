<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Students
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // for portal login
            $table->string('admission_number');
            $table->string('student_id_code');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->default('male'); // male, female, other
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('qualification')->nullable(); // High School, Intermediate, Graduate, Postgraduate, etc.
            $table->string('photo')->nullable();
            $table->date('admission_date');
            $table->string('status')->default('active'); // active, completed, dropped, suspended
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['franchise_id', 'admission_number']);
            $table->unique(['franchise_id', 'student_id_code']);
        });

        // 2. Student Enrollments
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->date('enrollment_date');
            $table->decimal('total_agreed_fee', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_fee', 10, 2);
            $table->string('status')->default('enrolled'); // enrolled, completed, dropped
            $table->timestamps();

            $table->unique(['student_id', 'batch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('students');
    }
};
