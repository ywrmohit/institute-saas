<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StudentPortalController;
use Illuminate\Support\Facades\Route;

// Defensive POST handlers for Filament login routes (fallback for direct/non-JS form posts)
Route::post('/admin/login', function (\Illuminate\Http\Request $request) {
    $email = $request->input('email') ?? $request->input('data.email');
    $password = $request->input('password') ?? $request->input('data.password');
    $remember = (bool) ($request->input('remember') ?? $request->input('data.remember', false));

    if ($email && $password && \Illuminate\Support\Facades\Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
        $request->session()->regenerate();
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->role === 'super_admin') {
            return redirect('/admin');
        }
        if ($user->franchise) {
            return redirect('/app/' . $user->franchise->slug);
        }
        return redirect('/admin');
    }

    return back()->withErrors([
        'data.email' => 'These credentials do not match our records.',
    ]);
})->middleware('web');

Route::post('/app/login', function (\Illuminate\Http\Request $request) {
    $email = $request->input('email') ?? $request->input('data.email');
    $password = $request->input('password') ?? $request->input('data.password');
    $remember = (bool) ($request->input('remember') ?? $request->input('data.remember', false));

    if ($email && $password && \Illuminate\Support\Facades\Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
        $request->session()->regenerate();
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->franchise) {
            return redirect('/app/' . $user->franchise->slug);
        }
        if ($user->role === 'super_admin') {
            return redirect('/admin');
        }
        return redirect('/app');
    }

    return back()->withErrors([
        'data.email' => 'These credentials do not match our records.',
    ]);
})->middleware('web');

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
