<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Exam;
use App\Models\FeeInvoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudyMaterial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function getProfile(int $id): JsonResponse
    {
        $student = Student::with(['branch', 'franchise', 'enrollments.course', 'enrollments.batch'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student,
                'attendance_percentage' => $student->calculateAttendancePercentage(),
            ],
        ]);
    }

    public function getFees(int $id): JsonResponse
    {
        $invoices = FeeInvoice::with('installments')->where('student_id', $id)->get();
        $payments = Payment::where('student_id', $id)->latest()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_amount' => $invoices->sum('total_amount'),
                'paid_amount' => $invoices->sum('paid_amount'),
                'pending_amount' => $invoices->sum('pending_amount'),
                'invoices' => $invoices,
                'payments' => $payments,
            ],
        ]);
    }

    public function getMaterials(int $id): JsonResponse
    {
        $student = Student::with('enrollments')->findOrFail($id);
        $courseIds = $student->enrollments->pluck('course_id');
        $batchIds = $student->enrollments->pluck('batch_id');

        $materials = StudyMaterial::whereIn('course_id', $courseIds)
            ->where(function ($q) use ($batchIds) {
                $q->whereNull('batch_id')->orWhereIn('batch_id', $batchIds);
            })
            ->where('is_published', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $materials,
        ]);
    }

    public function getCertificates(int $id): JsonResponse
    {
        $certificates = Certificate::with('course')->where('student_id', $id)->where('status', 'issued')->get();

        return response()->json([
            'success' => true,
            'data' => $certificates,
        ]);
    }
}
