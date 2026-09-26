<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StudentPortalController;
use Illuminate\Support\Facades\Route;

// Public Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public Certificate Verification & Printing
Route::get('/verify-certificate/{code?}', [CertificateController::class, 'verify'])->name('certificate.verify');
Route::post('/verify-certificate/search', [CertificateController::class, 'search'])->name('certificate.search');
Route::get('/certificate/{code}/print', [CertificateController::class, 'print'])->name('certificate.print');

// Printable Fee Receipt
Route::get('/receipt/{receiptNumber}/print', [ReceiptController::class, 'print'])->name('receipt.print');

// Student Portal Routes
Route::prefix('student')->group(function () {
    Route::get('/login', [StudentPortalController::class, 'showLogin'])->name('student.login');
    Route::post('/login', [StudentPortalController::class, 'login'])->name('student.login.submit');
    Route::post('/logout', [StudentPortalController::class, 'logout'])->name('student.logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/exams/{id}', [StudentPortalController::class, 'takeExam'])->name('student.exam.take');
        Route::post('/exams/{id}/submit', [StudentPortalController::class, 'submitExam'])->name('student.exam.submit');
    });
});
