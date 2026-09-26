<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;

class ExamService
{
    /**
     * Grade an MCQ submission.
     * $answers = [ question_id => 'selected_option_key' ]
     */
    public function gradeMcqExam(Exam $exam, Student $student, array $answers): ExamResult
    {
        $questions = $exam->questions;
        $totalMarks = $exam->total_marks;
        $marksObtained = 0;

        foreach ($questions as $question) {
            $userAns = $answers[$question->id] ?? null;
            if ($userAns !== null && trim((string)$userAns) === trim((string)$question->correct_answer)) {
                $marksObtained += $question->marks;
            }
        }

        $percentage = $totalMarks > 0 ? round(($marksObtained / $totalMarks) * 100, 2) : 0;
        $grade = ExamResult::calculateGrade($percentage);
        $status = ($marksObtained >= $exam->passing_marks) ? 'pass' : 'fail';

        $result = ExamResult::updateOrCreate(
            [
                'franchise_id' => $exam->franchise_id,
                'exam_id' => $exam->id,
                'student_id' => $student->id,
            ],
            [
                'marks_obtained' => $marksObtained,
                'total_marks' => $totalMarks,
                'percentage' => $percentage,
                'grade' => $grade,
                'status' => $status,
                'graded_by_user_id' => auth()->id(),
            ]
        );

        AuditLog::log('exam_submitted', $result, null, $result->toArray(), $exam->franchise_id);

        return $result;
    }
}
