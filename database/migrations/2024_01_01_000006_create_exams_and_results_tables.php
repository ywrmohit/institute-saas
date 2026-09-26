<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Exams
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->string('title');
            $table->string('exam_type')->default('mcq'); // mcq, practical, theory, hybrid
            $table->unsignedInteger('total_marks')->default(100);
            $table->unsignedInteger('passing_marks')->default(40);
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->dateTime('exam_date')->nullable();
            $table->string('status')->default('published'); // draft, published, completed
            $table->text('instructions')->nullable();
            $table->timestamps();
        });

        // 2. Exam Questions (for MCQ / Online exams)
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->text('question_text');
            $table->string('question_type')->default('single_choice'); // single_choice, true_false
            $table->json('options'); // ["A: ...", "B: ...", "C: ...", "D: ..."]
            $table->string('correct_answer'); // e.g. "A" or "true"
            $table->unsignedInteger('marks')->default(1);
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

        // 3. Exam Results
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('marks_obtained', 5, 2);
            $table->decimal('total_marks', 5, 2);
            $table->decimal('percentage', 5, 2);
            $table->string('grade')->default('B'); // A+, A, B, C, D, F
            $table->string('status')->default('pass'); // pass, fail
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exams');
    }
};
