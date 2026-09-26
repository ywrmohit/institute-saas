<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('certificate_number')->unique();
            $table->string('verification_code')->unique(); // UUID / Unique Token for QR code lookup
            $table->date('issue_date');
            $table->date('completion_date');
            $table->string('grade')->nullable(); // Distinction, A+, A, etc.
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('template_name')->default('classic_blue');
            $table->string('status')->default('issued'); // issued, revoked
            $table->timestamps();

            $table->index('verification_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
