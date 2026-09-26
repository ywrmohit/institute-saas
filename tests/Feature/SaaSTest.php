<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Exam;
use App\Models\FeeInvoice;
use App\Models\Franchise;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use App\Services\AiInsightService;
use App\Services\CertificateService;
use App\Services\ExamService;
use App\Services\FeeService;
use Tests\TestCase;

class SaaSTest extends TestCase
{
    /** Test 1: Public Landing page loads */
    public function test_landing_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('EduPulse');
        $response->assertSee('Super Admin Panel');
        $response->assertSee('Student Portal');
    }

    /** Test 2: Public Certificate Verification with valid code */
    public function test_certificate_verification_with_valid_code(): void
    {
        $cert = Certificate::first();
        $this->assertNotNull($cert);

        $response = $this->get('/verify-certificate/' . $cert->verification_code);
        $response->assertStatus(200);
        $response->assertSee('Authentic Certificate Verified');
        $response->assertSee($cert->student->full_name);
        $response->assertSee($cert->course->name);
    }

    /** Test 3: Public Certificate Verification with invalid code */
    public function test_certificate_verification_with_invalid_code(): void
    {
        $response = $this->get('/verify-certificate/non-existent-code-xyz');
        $response->assertStatus(200);
        $response->assertSee('No Matching Certificate Record Found');
    }

    /** Test 4: Official Certificate Print Template */
    public function test_certificate_print_template_renders(): void
    {
        $cert = Certificate::first();
        $response = $this->get('/certificate/' . $cert->verification_code . '/print');
        $response->assertStatus(200);
        $response->assertSee($cert->certificate_number);
        $response->assertSee($cert->student->full_name);
        $response->assertSee('Certificate of Completion');
    }

    /** Test 5: Printable Receipt view */
    public function test_payment_receipt_print_template_renders(): void
    {
        $payment = Payment::first();
        $this->assertNotNull($payment);

        $response = $this->get('/receipt/' . $payment->receipt_number . '/print');
        $response->assertStatus(200);
        $response->assertSee($payment->receipt_number);
        $response->assertSee($payment->student->full_name);
        $response->assertSee('Payment Receipt');
    }

    /** Test 6: Student Portal Login */
    public function test_student_can_login_to_portal(): void
    {
        $response = $this->post('/student/login', [
            'login' => 'student@apextech.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/student/dashboard');
        $this->assertAuthenticated();
    }

    /** Test 7: Student Dashboard displays enrolled data */
    public function test_authenticated_student_can_view_dashboard(): void
    {
        $studentUser = User::where('email', 'student@apextech.com')->first();
        $response = $this->actingAs($studentUser)->get('/student/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Aarav Sharma');
        $response->assertSee('Active Enrollments');
        $response->assertSee('Daily Attendance Record');
        $response->assertSee('Fee Invoices');
    }

    /** Test 8: Online MCQ Exam Evaluation */
    public function test_online_mcq_exam_submission_and_scoring(): void
    {
        $studentUser = User::where('email', 'student@apextech.com')->first();
        $student = Student::where('user_id', $studentUser->id)->first();
        $exam = Exam::with('questions')->first();

        $this->assertNotNull($exam);
        $this->assertGreaterThan(0, $exam->questions->count());

        $answers = [];
        foreach ($exam->questions as $q) {
            $answers[$q->id] = $q->correct_answer;
        }

        $examService = app(ExamService::class);
        $result = $examService->gradeMcqExam($exam, $student, $answers);

        $this->assertEquals($exam->total_marks, $result->marks_obtained);
        $this->assertEquals(100.0, $result->percentage);
        $this->assertEquals('A+', $result->grade);
        $this->assertEquals('pass', $result->status);
    }

    /** Test 9: Strict Multi-Tenancy Data Isolation */
    public function test_multi_tenancy_data_isolation_between_franchises(): void
    {
        $franchise1 = Franchise::where('code', 'APEX01')->first();
        $franchise2 = Franchise::where('code', 'NXG02')->first();

        $this->assertNotNull($franchise1);
        $this->assertNotNull($franchise2);

        // Under tenant 1 scope
        app()->instance('current_tenant_id', $franchise1->id);
        $f1Students = Student::all();
        foreach ($f1Students as $s) {
            $this->assertEquals($franchise1->id, $s->franchise_id);
            $this->assertNotEquals($franchise2->id, $s->franchise_id);
        }

        // Under tenant 2 scope
        app()->instance('current_tenant_id', $franchise2->id);
        $f2Students = Student::all();
        $this->assertEquals(0, $f2Students->count());

        // Reset tenant
        app()->forgetInstance('current_tenant_id');
    }

    /** Test 10: Fee Service Installment & Payment Calculation */
    public function test_fee_service_invoice_and_payment_recalculation(): void
    {
        $invoice = FeeInvoice::first();
        $this->assertNotNull($invoice);

        $initialPending = $invoice->pending_amount;

        $feeService = app(FeeService::class);
        $payment = $feeService->recordPayment(
            invoice: $invoice,
            amount: 1000.00,
            paymentMethod: 'cash',
            remarks: 'Test installment settlement'
        );

        $invoice->refresh();
        $this->assertEquals($initialPending - 1000.00, $invoice->pending_amount);
    }

    /** Test 11: AI Retention & Risk Insight Service */
    public function test_ai_insight_service_calculates_student_risk(): void
    {
        $student = Student::first();
        $aiService = app(AiInsightService::class);
        $insight = $aiService->assessStudentRisk($student);

        $this->assertArrayHasKey('risk_level', $insight);
        $this->assertArrayHasKey('attendance_percentage', $insight);
        $this->assertArrayHasKey('recommendation', $insight);
        $this->assertTrue($insight['ai_ready']);
    }

    /** Test 12: API V1 Public Certificate Endpoint */
    public function test_api_v1_verify_certificate_endpoint(): void
    {
        $cert = Certificate::first();
        $response = $this->getJson('/api/v1/verify-certificate/' . $cert->verification_code);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'certificate_number' => $cert->certificate_number,
                'status' => 'issued',
            ],
        ]);
    }

    /** Test 13: Super Admin Access Authorization */
    public function test_super_admin_panel_access_contract(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $this->assertNotNull($superAdmin);
        $this->assertTrue($superAdmin->isSuperAdmin());

        $franchiseUser = User::where('role', 'franchise_owner')->first();
        $this->assertFalse($franchiseUser->isSuperAdmin());
    }

    /** Test 14: Certificate Service Generation */
    public function test_certificate_service_issues_unique_credentials(): void
    {
        $student = Student::first();
        $course = Course::first();

        $certService = app(CertificateService::class);
        $cert = $certService->issueCertificate($student, $course, 'First Class', 76.5);

        $this->assertNotNull($cert->certificate_number);
        $this->assertNotNull($cert->verification_code);
        $this->assertEquals('issued', $cert->status);
        $this->assertEquals('First Class', $cert->grade);
        $this->assertEquals(76.5, $cert->percentage);
    }

    /** Test 15: Attendance Percentage Calculation */
    public function test_student_attendance_percentage_calculation(): void
    {
        $student = Student::first();
        $percentage = $student->calculateAttendancePercentage();

        $this->assertIsFloat($percentage);
        $this->assertGreaterThanOrEqual(0.0, $percentage);
        $this->assertLessThanOrEqual(100.0, $percentage);
    }

    /** Test 16: Franchise Panel Tenant Access */
    public function test_franchise_panel_tenant_routing(): void
    {
        $franchise = Franchise::first();
        $owner = User::where('role', 'franchise_owner')->where('franchise_id', $franchise->id)->first();

        $response = $this->actingAs($owner)->get("/app/{$franchise->slug}");
        $response->assertStatus(200);
    }
}
