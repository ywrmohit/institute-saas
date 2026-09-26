<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CertificateService
{
    public function issueCertificate(
        Student $student,
        Course $course,
        ?string $grade = null,
        ?float $percentage = null,
        ?Carbon $completionDate = null,
        string $templateName = 'classic_blue'
    ): Certificate {
        $completionDate = $completionDate ?? Carbon::today();
        $issueDate = Carbon::today();
        $certificateNumber = 'CERT-' . date('Y') . '-' . strtoupper(Str::random(8));
        $verificationCode = (string) Str::uuid();

        $certificate = Certificate::create([
            'franchise_id' => $student->franchise_id,
            'branch_id' => $student->branch_id,
            'student_id' => $student->id,
            'course_id' => $course->id,
            'certificate_number' => $certificateNumber,
            'verification_code' => $verificationCode,
            'issue_date' => $issueDate,
            'completion_date' => $completionDate,
            'grade' => $grade ?? 'Distinction',
            'percentage' => $percentage,
            'template_name' => $templateName,
            'status' => 'issued',
        ]);

        AuditLog::log('certificate_issued', $certificate, null, $certificate->toArray(), $student->franchise_id);

        return $certificate;
    }
}
