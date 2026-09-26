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
     * Authenticate student using email or student ID code.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt login via email or student ID code
        $user = User::where('role', 'student')
            ->where(function ($query) use ($credentials) {
                $query->where('email', $credentials['login'])
                    ->orWhereHas('student', function ($q) use ($credentials) {
                        $q->where('student_id_code', $credentials['login'])
                          ->orWhere('admission_number', $credentials['login']);
                    });
            })
            ->first();

        if ($user && Auth::attempt(['email' => $user->email, 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('student.dashboard'));
        }

        return back()->withErrors([
            'login' => 'Invalid credentials. Please verify your Student ID/Email and password.',
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
