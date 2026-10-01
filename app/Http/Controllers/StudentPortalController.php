<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\FeeInvoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudyMaterial;
use App\Models\User;
use App\Services\ExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentPortalController extends Controller
{
    /**
     * Show student login page.
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isStudent()) {
            return redirect()->route('student.dashboard');
        }

        return view('student.login');
    }

    /**
     * Authenticate student using email, student ID code, admission number, or mobile phone.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($credentials['login']);
        $cleanedPhone = preg_replace('/[^0-9]/', '', $login);

        // First attempt finding a student user
        $user = User::where(function ($query) use ($login, $cleanedPhone) {
                $query->where('email', $login)
                    ->orWhere('phone', $login);

                if (!empty($cleanedPhone) && strlen($cleanedPhone) >= 7) {
                    $query->orWhere('phone', 'like', '%' . substr($cleanedPhone, -10));
                }

                $query->orWhereHas('student', function ($q) use ($login, $cleanedPhone) {
                    $q->where('student_id_code', $login)
                      ->orWhere('admission_number', $login)
                      ->orWhere('phone', $login);

                    if (!empty($cleanedPhone) && strlen($cleanedPhone) >= 7) {
                        $q->orWhere('phone', 'like', '%' . substr($cleanedPhone, -10));
                    }
                });
            })
            ->first();

        // If not found as student, check any registered staff user attempting login
        if (! $user) {
            $user = User::where('email', $login)->orWhere('phone', $login)->first();
        }

        if ($user && $user->status !== 'active') {
            return back()->withErrors([
                'login' => 'Your account has been deactivated. Please contact your center administrator.',
            ])->withInput($request->only('login'));
        }

        if ($user && Auth::attempt(['email' => $user->email, 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Smooth cross-role redirection if staff logged in here
            if ($user->isSuperAdmin()) {
                return redirect()->intended('/admin');
            }

            if (! $user->isStudent()) {
                if ($user->franchise) {
                    return redirect()->intended('/app/' . $user->franchise->slug);
                }
                return redirect()->intended('/app');
            }

            return redirect()->intended(route('student.dashboard'));
        }

        return back()->withErrors([
            'login' => 'Invalid credentials. Please verify your Student ID, Email or Phone, and password.',
        ])->withInput($request->only('login'));
    }

    /**
     * Student Portal Dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $student = Student::with([
            'branch',
            'franchise',
            'enrollments.course',
            'enrollments.batch.trainer',
        ])->where('user_id', $user->id)->first();

        if (!$student) {
            // Check if student record exists matching email
            $student = Student::with([
                'branch',
                'franchise',
                'enrollments.course',
                'enrollments.batch.trainer',
            ])->where('email', $user->email)->first();
        }

        if (!$student) {
            return view('student.no-profile');
        }

        $enrollments = $student->enrollments;
        $courseIds = $enrollments->pluck('course_id');
        $batchIds = $enrollments->pluck('batch_id');

        // Attendance stats
        $attendancePercentage = $student->calculateAttendancePercentage();
        $recentAttendances = Attendance::where('student_id', $student->id)
            ->latest('date')
            ->limit(10)
            ->get();

        // Financials
        $invoices = FeeInvoice::with('installments')->where('student_id', $student->id)->get();
        $payments = Payment::where('student_id', $student->id)->latest('payment_date')->get();
        $totalFees = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('paid_amount');
        $totalPending = $invoices->sum('pending_amount');

        // Study Materials
        $studyMaterials = StudyMaterial::whereIn('course_id', $courseIds)
            ->where(function ($q) use ($batchIds) {
                $q->whereNull('batch_id')->orWhereIn('batch_id', $batchIds);
            })
            ->where('is_published', true)
            ->latest()
            ->limit(8)
            ->get();

        // Exams & Results
        $exams = Exam::whereIn('course_id', $courseIds)
            ->where(function ($q) use ($batchIds) {
                $q->whereNull('batch_id')->orWhereIn('batch_id', $batchIds);
            })
            ->where('status', 'published')
            ->latest()
            ->get();

        $examResults = ExamResult::with('exam')
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        // Certificates
        $certificates = Certificate::with('course')
            ->where('student_id', $student->id)
            ->where('status', 'issued')
            ->get();

        // Announcements
        $announcements = Announcement::where('franchise_id', $student->franchise_id)
            ->whereIn('target_role', ['all', 'students'])
            ->where('is_active', true)
            ->latest()
            ->limit(5)
            ->get();

        return view('student.dashboard', compact(
            'student',
            'enrollments',
            'attendancePercentage',
            'recentAttendances',
            'invoices',
            'payments',
            'totalFees',
            'totalPaid',
            'totalPending',
            'studyMaterials',
            'exams',
            'examResults',
            'certificates',
            'announcements'
        ));
    }

    /**
     * Online MCQ Test Taker.
     */
    public function takeExam(int $id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->orWhere('email', $user->email)->firstOrFail();

        $exam = Exam::with('questions')->findOrFail($id);

        // Check if student already submitted
        $existingResult = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        return view('student.exam-taker', compact('exam', 'student', 'existingResult'));
    }

    /**
     * Submit MCQ Exam.
     */
    public function submitExam(Request $request, int $id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->orWhere('email', $user->email)->firstOrFail();
        $exam = Exam::with('questions')->findOrFail($id);

        $answers = $request->input('answers', []);
        $examService = app(ExamService::class);
        $result = $examService->gradeMcqExam($exam, $student, $answers);

        return redirect()->route('student.exam.take', ['id' => $exam->id])
            ->with('status', "Exam submitted successfully! You scored {$result->marks_obtained} / {$result->total_marks} ({$result->percentage}% - Grade {$result->grade})");
    }

    /**
     * Print official high-resolution Student ID Card.
     */
    public function printIdCard($id)
    {
        $student = Student::with(['branch', 'enrollments.course', 'enrollments.batch.trainer'])->findOrFail($id);

        if (Auth::check() && Auth::user()->isStudent() && Auth::user()->student?->id !== $student->id) {
            abort(403, 'Unauthorized access to ID card.');
        }

        return view('student.id-card', compact('student'));
    }

    /**
     * Show Student Profile screen.
     */
    public function profile()
    {
        $user = Auth::user();
        $student = Student::with([
            'branch',
            'franchise',
            'enrollments.course',
            'enrollments.batch.trainer',
        ])->where('user_id', $user->id)->orWhere('email', $user->email)->first();

        if (!$student) {
            return view('student.no-profile');
        }

        $attendancePercentage = $student->calculateAttendancePercentage();
        $certificates = Certificate::with('course')->where('student_id', $student->id)->get();

        return view('student.profile', compact('student', 'user', 'attendancePercentage', 'certificates'));
    }

    /**
     * Update Student Personal Profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->orWhere('email', $user->email)->firstOrFail();

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20',
            'guardian_name' => 'nullable|string|max:100',
            'guardian_phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'qualification' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
        ]);

        $student->update($validated);

        // Keep user name & phone in sync
        $fullName = trim("{$request->first_name} {$request->last_name}");
        $user->update([
            'name' => $fullName,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Profile information updated successfully.');
    }

    /**
     * Update Student Profile Photo.
     */
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->orWhere('email', $user->email)->firstOrFail();

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Delete old photo if exists
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        $path = $request->file('photo')->store('students/photos', 'public');

        $student->update(['photo' => $path]);
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Profile photo updated and synced with your official ID card.');
    }

    /**
     * Update Student Account Password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully. Please use your new password next time you sign in.');
    }

    /**
     * Logout from student portal.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}
