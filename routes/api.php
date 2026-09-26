<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\CertificateController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth endpoints
    Route::post('/login', [AuthController::class, 'login']);

    // Student endpoints (ready for mobile apps)
    Route::prefix('students')->group(function () {
        Route::get('/{id}/profile', [StudentController::class, 'getProfile']);
        Route::get('/{id}/fees', [StudentController::class, 'getFees']);
        Route::get('/{id}/materials', [StudentController::class, 'getMaterials']);
        Route::get('/{id}/certificates', [StudentController::class, 'getCertificates']);
    });

    // Public Certificate Verification API endpoint
    Route::get('/verify-certificate/{code}', function ($code) {
        $cert = \App\Models\Certificate::with(['student', 'course', 'branch', 'franchise'])
            ->where('verification_code', $code)
            ->orWhere('certificate_number', $code)
            ->first();

        if (!$cert) {
            return response()->json([
                'success' => false,
                'message' => 'Certificate not found or invalid',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'certificate_number' => $cert->certificate_number,
                'student_name' => $cert->student->full_name,
                'course' => $cert->course->name,
                'franchise' => $cert->franchise->name,
                'branch' => $cert->branch->name,
                'issue_date' => $cert->issue_date->format('Y-m-d'),
                'completion_date' => $cert->completion_date->format('Y-m-d'),
                'grade' => $cert->grade,
                'percentage' => $cert->percentage,
                'status' => $cert->status,
            ],
        ]);
    });
});
