<?php

namespace App\Services;

use App\Models\Student;

class AiInsightService
{
    /**
     * Compute a risk assessment for a student based on attendance, exam scores, and fee status.
     * Ready for AI LLM enhancement or rule-based heuristic prediction.
     */
    public function assessStudentRisk(Student $student): array
    {
        $attendancePct = $student->calculateAttendancePercentage();
        $pendingFee = (float) $student->feeInvoices()->sum('pending_amount');
        $avgExamScore = (float) $student->examResults()->avg('percentage') ?? 100;

        $riskLevel = 'low';
        $riskFactors = [];

        if ($attendancePct < 60) {
            $riskFactors[] = "Low attendance ({$attendancePct}%)";
            $riskLevel = 'high';
        } elseif ($attendancePct < 75) {
            $riskFactors[] = "Moderate attendance deficit ({$attendancePct}%)";
            if ($riskLevel !== 'high') $riskLevel = 'medium';
        }

        if ($avgExamScore < 40) {
            $riskFactors[] = "Academic struggles: average exam score {$avgExamScore}%";
            $riskLevel = 'high';
        } elseif ($avgExamScore < 60) {
            $riskFactors[] = "Borderline academic performance ({$avgExamScore}%)";
            if ($riskLevel !== 'high') $riskLevel = 'medium';
        }

        if ($pendingFee > 5000) {
            $riskFactors[] = "Overdue fees pending: ₹" . number_format($pendingFee, 2);
            if ($riskLevel === 'low') $riskLevel = 'medium';
        }

        $summary = match ($riskLevel) {
            'high' => "Student is at critical dropout/failure risk. Immediate counselor or trainer intervention recommended.",
            'medium' => "Student exhibits performance or attendance irregularities that require monitoring.",
            default => "Student is performing well academically and maintaining healthy attendance.",
        };

        return [
            'student_id' => $student->id,
            'student_name' => $student->full_name,
            'risk_level' => $riskLevel,
            'attendance_percentage' => $attendancePct,
            'average_exam_percentage' => round($avgExamScore, 1),
            'pending_fee' => $pendingFee,
            'risk_factors' => $riskFactors,
            'recommendation' => $summary,
            'ai_ready' => true,
        ];
    }
}
