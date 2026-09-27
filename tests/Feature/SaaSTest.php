<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Batch;
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
        $student = Student::first();
        $this->assertNotNull($student);

        $invoice = FeeInvoice::create([
            'franchise_id' => $student->franchise_id,
            'branch_id' => $student->branch_id,
            'student_id' => $student->id,
            'invoice_number' => 'INV-TEST-' . uniqid(),
            'total_amount' => 5000.00,
            'paid_amount' => 0.00,
            'pending_amount' => 5000.00,
            'status' => 'pending',
            'due_date' => now()->addDays(30),
        ]);

        $feeService = app(FeeService::class);
        $payment = $feeService->recordPayment(
            invoice: $invoice,
            amount: 1000.00,
            paymentMethod: 'cash',
            remarks: 'Test installment settlement'
        );

        $invoice->refresh();
        $this->assertEquals(4000.00, $invoice->pending_amount);
        $this->assertEquals(1000.00, $invoice->paid_amount);
        $this->assertEquals('partially_paid', $invoice->status);
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

    /** Test 17: Admin Login Native POST Fallback */
    public function test_admin_login_post_redirects_super_admin(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@edupulse.io',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticated();
    }

    /** Test 18: Franchise App Login Native POST Fallback */
    public function test_app_login_post_redirects_franchise_owner(): void
    {
        $franchise = Franchise::first();
        $owner = User::where('role', 'franchise_owner')->where('franchise_id', $franchise->id)->first();

        $response = $this->post('/app/login', [
            'email' => $owner->email,
            'password' => 'password',
        ]);

        $response->assertRedirect("/app/{$franchise->slug}");
        $this->assertAuthenticated();
    }

    /** Test 19: Admin Login Invalid Credentials Fails Gracefully */
    public function test_admin_login_post_with_invalid_credentials_fails(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@edupulse.io',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('data.email');
        $this->assertGuest();
    }

    /** Test 20: Livewire Admin Login Smoothly Routes Franchise Owner to Tenant Hub */
    public function test_livewire_admin_login_redirects_franchise_owner_to_tenant_portal(): void
    {
        \Livewire\Livewire::test(\App\Filament\Pages\Auth\AdminLogin::class)
            ->set('data.email', 'owner@apextech.com')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertRedirect();

        $this->assertAuthenticated();
        $this->assertEquals('franchise_owner', auth()->user()->role);
    }

    /** Test 21: Livewire Franchise Login Authenticates Franchise Staff */
    public function test_livewire_franchise_login_authenticates_franchise_owner(): void
    {
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('franchise'));

        \Livewire\Livewire::test(\App\Filament\Pages\Auth\FranchiseLogin::class)
            ->set('data.email', 'owner@apextech.com')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertRedirect();

        $this->assertAuthenticated();
    }

    /** Test 22: Printable CR80 PVC Student ID Card renders with student data & QR code */
    public function test_student_id_card_renders_successfully(): void
    {
        $student = Student::first();
        $this->assertNotNull($student);

        $response = $this->get("/student/{$student->id}/id-card");
        $response->assertStatus(200);
        $response->assertSee($student->full_name);
        $response->assertSee($student->student_id_code);
        $response->assertSee('Official Student Identity Card');
        $response->assertSee('Print ID Card');
    }

    /** Test 23: Strict RBAC Navigation Scoping across Trainer and Accountant */
    public function test_strict_rbac_navigation_scoping_for_different_roles(): void
    {
        // 1. Trainer Role Isolation
        $trainer = User::where('email', 'trainer@apextech.com')->first();
        $this->actingAs($trainer);

        $this->assertFalse(\App\Filament\Franchise\Resources\FeeInvoiceResource::shouldRegisterNavigation());
        $this->assertFalse(\App\Filament\Franchise\Resources\PaymentResource::shouldRegisterNavigation());
        $this->assertFalse(\App\Filament\Franchise\Resources\BranchResource::shouldRegisterNavigation());
        $this->assertTrue(\App\Filament\Franchise\Resources\BatchResource::shouldRegisterNavigation());
        $this->assertTrue(\App\Filament\Franchise\Resources\CourseResource::shouldRegisterNavigation());
        $this->assertTrue(\App\Filament\Franchise\Resources\AttendanceResource::shouldRegisterNavigation());

        // 2. Accountant Role Isolation
        $accountant = User::where('email', 'accountant@apextech.com')->first();
        $this->actingAs($accountant);

        $this->assertTrue(\App\Filament\Franchise\Resources\FeeInvoiceResource::shouldRegisterNavigation());
        $this->assertTrue(\App\Filament\Franchise\Resources\PaymentResource::shouldRegisterNavigation());
        $this->assertFalse(\App\Filament\Franchise\Resources\CourseResource::shouldRegisterNavigation());
        $this->assertFalse(\App\Filament\Franchise\Resources\BatchResource::shouldRegisterNavigation());
        $this->assertFalse(\App\Filament\Franchise\Resources\ExamResource::shouldRegisterNavigation());
        $this->assertFalse(\App\Filament\Franchise\Resources\CertificateResource::shouldRegisterNavigation());
    }

    /** Test 24: Role Switcher Quick Fill sets credentials and authenticates role */
    public function test_franchise_login_role_switcher_and_quick_fill(): void
    {
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('franchise'));

        // Test Trainer role switch & form fill
        $component = \Livewire\Livewire::test(\App\Filament\Pages\Auth\FranchiseLogin::class)
            ->call('selectRole', 'trainer')
            ->assertSet('selectedRole', 'trainer');

        $this->assertEquals('trainer@apextech.com', $component->get('data.email'));

        // Test Accountant role switch & form fill
        $component->call('selectRole', 'accountant')
            ->assertSet('selectedRole', 'accountant');

        $this->assertEquals('accountant@apextech.com', $component->get('data.email'));
    }

    /** Test 25: 1-Step Admission Flow creates student, enrollment, invoice, installments & portal user */
    public function test_one_step_admission_flow_automates_enrollment_and_billing(): void
    {
        $franchise = Franchise::first();
        $branch = $franchise->branches()->first();
        $course = Course::where('franchise_id', $franchise->id)->first();
        $batch = Batch::where('course_id', $course->id)->first();
        $owner = User::where('email', 'owner@apextech.com')->first();

        $this->actingAs($owner);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('franchise'));
        \Filament\Facades\Filament::setTenant($franchise);

        Student::where('email', 'kavita.patel@testmail.com')->delete();
        User::where('email', 'kavita.patel@testmail.com')->delete();

        \Livewire\Livewire::test(\App\Filament\Franchise\Resources\StudentResource\Pages\CreateStudent::class)
            ->fillForm([
                'first_name' => 'Kavita',
                'last_name' => 'Patel',
                'branch_id' => $branch->id,
                'phone' => '+91 98980 11223',
                'email' => 'kavita.patel@testmail.com',
                'gender' => 'female',
                'date_of_birth' => '2004-05-15',
                'enrolled_course_id' => $course->id,
                'enrolled_batch_id' => $batch?->id,
                'course_fee' => 12000,
                'discount_amount' => 2000,
                'net_fee' => 10000,
                'installments_count' => 2,
                'auto_create_portal_user' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        // 1. Verify Student was created
        $student = Student::where('email', 'kavita.patel@testmail.com')->latest('id')->first();
        $this->assertNotNull($student);
        $this->assertEquals('Kavita Patel', $student->full_name);

        // 2. Verify Enrollment was auto-created
        $enrollment = \App\Models\Enrollment::where('student_id', $student->id)->latest('id')->first();
        $this->assertNotNull($enrollment);
        $this->assertEquals($course->id, $enrollment->course_id);

        // 3. Verify Fee Invoice was auto-created with 2 installments
        $invoice = FeeInvoice::where('student_id', $student->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(10000.00, (float)$invoice->total_amount);
        $this->assertCount(2, $invoice->installments);

        // 4. Verify Student User Login Account was created
        $user = User::where('email', 'kavita.patel@testmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('student', $user->role);
        $this->assertEquals($user->id, $student->user_id);
    }

    /** Test 26: Spatie Role and Permission Matrix Verification */
    public function test_spatie_role_and_permission_assignment(): void
    {
        // 1. Trainer Role and Permissions
        $trainer = User::where('email', 'trainer@apextech.com')->first();
        $this->assertNotNull($trainer);
        $this->assertTrue($trainer->hasRole('trainer'));
        $this->assertTrue($trainer->can('mark_attendance'));
        $this->assertTrue($trainer->can('view_attendance'));
        $this->assertTrue($trainer->can('view_courses'));
        $this->assertTrue($trainer->can('view_batches'));
        $this->assertFalse($trainer->can('view_fee_invoices'));
        $this->assertFalse($trainer->can('record_payments'));
        $this->assertFalse($trainer->can('manage_branches'));
        $this->assertFalse($trainer->can('manage_roles_permissions'));

        // 2. Accountant Role and Permissions
        $accountant = User::where('email', 'accountant@apextech.com')->first();
        $this->assertNotNull($accountant);
        $this->assertTrue($accountant->hasRole('accountant'));
        $this->assertTrue($accountant->can('view_fee_invoices'));
        $this->assertTrue($accountant->can('manage_fee_invoices'));
        $this->assertTrue($accountant->can('record_payments'));
        $this->assertFalse($accountant->can('manage_courses'));
        $this->assertFalse($accountant->can('manage_batches'));
        $this->assertFalse($accountant->can('issue_certificates'));
        $this->assertFalse($accountant->can('manage_roles_permissions'));

        // 3. Franchise Owner Role and Permissions
        $owner = User::where('email', 'owner@apextech.com')->first();
        $this->assertNotNull($owner);
        $this->assertTrue($owner->hasRole('franchise_owner'));
        $this->assertTrue($owner->can('manage_roles_permissions'));
        $this->assertTrue($owner->can('create_students'));
        $this->assertTrue($owner->can('manage_fee_invoices'));
        $this->assertTrue($owner->can('manage_courses'));
    }

    /** Test 27: Custom Role Creation and Granular Resource Gates */
    public function test_custom_role_creation_and_permission_gate(): void
    {
        $franchise = Franchise::first();

        // 1. Create a custom role with isolated permissions
        $customRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'front_desk_counselor', 'guard_name' => 'web']);
        $customRole->syncPermissions(['view_students', 'create_students']);

        // 2. Create a staff user assigned to this custom role
        $counselor = User::updateOrCreate(
            ['email' => 'counselor@apextech.com'],
            [
                'name' => 'Counselor Priya',
                'franchise_id' => $franchise->id,
                'role' => 'front_desk_counselor',
                'status' => 'active',
                'password' => bcrypt('password'),
            ]
        );
        $counselor->syncRoles(['front_desk_counselor']);

        $this->actingAs($counselor);

        // 3. Assert permission gates
        $this->assertTrue($counselor->can('view_students'));
        $this->assertTrue($counselor->can('create_students'));
        $this->assertFalse($counselor->can('manage_fee_invoices'));
        $this->assertFalse($counselor->can('manage_courses'));
        $this->assertFalse($counselor->can('manage_roles_permissions'));

        // 4. Assert Resource Authorizations
        $this->assertTrue(\App\Filament\Franchise\Resources\StudentResource::canCreate());
        $this->assertFalse(\App\Filament\Franchise\Resources\FeeInvoiceResource::canCreate());
        $this->assertFalse(\App\Filament\Franchise\Resources\CourseResource::canCreate());
        $this->assertFalse(\App\Filament\Franchise\Resources\RoleResource::canViewAny());
    }

    /** Test 28: RoleResource Access Restricted to Authorized Staff */
    public function test_role_resource_access_restricted(): void
    {
        $owner = User::where('email', 'owner@apextech.com')->first();
        $this->actingAs($owner);
        $this->assertTrue(\App\Filament\Franchise\Resources\RoleResource::canViewAny());
        $this->assertTrue(\App\Filament\Franchise\Resources\RoleResource::canCreate());

        $trainer = User::where('email', 'trainer@apextech.com')->first();
        $this->actingAs($trainer);
        $this->assertFalse(\App\Filament\Franchise\Resources\RoleResource::canViewAny());
        $this->assertFalse(\App\Filament\Franchise\Resources\RoleResource::canCreate());

        $accountant = User::where('email', 'accountant@apextech.com')->first();
        $this->actingAs($accountant);
        $this->assertFalse(\App\Filament\Franchise\Resources\RoleResource::canViewAny());
        $this->assertFalse(\App\Filament\Franchise\Resources\RoleResource::canCreate());
    }
}
