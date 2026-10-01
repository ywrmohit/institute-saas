<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StudentPortalController;
use Illuminate\Support\Facades\Route;

// Defensive POST handlers for Filament login routes (fallback for direct/non-JS form posts)
Route::post('/admin/login', function (\Illuminate\Http\Request $request) {
    $login = trim($request->input('email') ?? $request->input('data.email') ?? '');
    $password = $request->input('password') ?? $request->input('data.password');
    $remember = (bool) ($request->input('remember') ?? $request->input('data.remember', false));

    $cleanedPhone = preg_replace('/[^0-9]/', '', $login);
    $user = \App\Models\User::where(function ($query) use ($login, $cleanedPhone) {
        $query->where('email', $login)->orWhere('phone', $login);
        if (!empty($cleanedPhone) && strlen($cleanedPhone) >= 7) {
            $query->orWhere('phone', 'like', '%' . substr($cleanedPhone, -10));
        }
    })->first();

    if ($user && \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
        if ($user->status !== 'active') {
            return back()->withErrors(['data.email' => 'Your account has been deactivated. Please contact administrator.']);
        }

        \Illuminate\Support\Facades\Auth::login($user, $remember);
        $request->session()->regenerate();

        if ($user->role === 'super_admin') {
            return redirect('/admin');
        }
        if ($user->role === 'student') {
            return redirect()->route('student.dashboard');
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
    $login = trim($request->input('email') ?? $request->input('data.email') ?? '');
    $password = $request->input('password') ?? $request->input('data.password');
    $remember = (bool) ($request->input('remember') ?? $request->input('data.remember', false));

    $cleanedPhone = preg_replace('/[^0-9]/', '', $login);
    $user = \App\Models\User::where(function ($query) use ($login, $cleanedPhone) {
        $query->where('email', $login)->orWhere('phone', $login);
        if (!empty($cleanedPhone) && strlen($cleanedPhone) >= 7) {
            $query->orWhere('phone', 'like', '%' . substr($cleanedPhone, -10));
        }
    })->first();

    if ($user && \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
        if ($user->status !== 'active') {
            return back()->withErrors(['data.email' => 'Your account has been deactivated. Please contact center administrator.']);
        }

        \Illuminate\Support\Facades\Auth::login($user, $remember);
        $request->session()->regenerate();

        if ($user->role === 'super_admin') {
            return redirect('/admin');
        }
        if ($user->role === 'student') {
            return redirect()->route('student.dashboard');
        }
        if ($user->franchise) {
            return redirect('/app/' . $user->franchise->slug);
        }
        return redirect('/app');
    }

    return back()->withErrors([
        'data.email' => 'These credentials do not match our records.',
    ]);
})->middleware('web');

// Smart root /app routing for authenticated franchise users
Route::get('/app', function () {
    if (! auth()->check()) {
        return redirect('/app/login');
    }
    $user = auth()->user();
    if ($user->isSuperAdmin()) {
        return redirect('/admin');
    }
    if ($user->isStudent()) {
        return redirect()->route('student.dashboard');
    }
    if ($user->franchise) {
        return redirect('/app/' . $user->franchise->slug);
    }
    return redirect('/app/login');
});

// Universal Logout Handlers (handles both GET and POST gracefully, preventing 405 Method Not Allowed)
Route::match(['get', 'post'], '/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    \Filament\Facades\Filament::auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/')->with('status', 'You have been signed out successfully.');
})->name('logout');

Route::get('/admin/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    \Filament\Facades\Filament::auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/admin/login')->with('status', 'You have been signed out successfully.');
});

Route::get('/app/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    \Filament\Facades\Filament::auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/app/login')->with('status', 'You have been signed out successfully.');
});

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
        Route::get('/profile', [StudentPortalController::class, 'profile'])->name('student.profile');
        Route::post('/profile', [StudentPortalController::class, 'updateProfile'])->name('student.profile.update');
        Route::post('/profile/photo', [StudentPortalController::class, 'updatePhoto'])->name('student.profile.photo');
        Route::post('/profile/password', [StudentPortalController::class, 'updatePassword'])->name('student.profile.password');
        Route::get('/exams/{id}', [StudentPortalController::class, 'takeExam'])->name('student.exam.take');
        Route::post('/exams/{id}/submit', [StudentPortalController::class, 'submitExam'])->name('student.exam.submit');
    });

    // Student ID Card Print (accessible with auth or franchise token)
    Route::get('/{id}/id-card', [StudentPortalController::class, 'printIdCard'])->name('student.id-card.print');
});
