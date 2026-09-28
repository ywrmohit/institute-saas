<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamResult;
use App\Models\FeeInstallment;
use App\Models\FeeInvoice;
use App\Models\Franchise;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudyMaterial;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        // 1. Subscription Plans
        $planStarter = SubscriptionPlan::create([
            'name' => 'Starter Academy',
            'slug' => 'starter-academy',
            'price' => 999.00,
            'billing_interval' => 'monthly',
            'max_branches' => 1,
            'max_students' => 150,
            'features' => ['Single Branch', 'Student Portal', 'Fee Receipts', 'Attendance Tracking'],
            'is_active' => true,
        ]);

        $planGrowth = SubscriptionPlan::create([
            'name' => 'Growth Franchise Network',
            'slug' => 'growth-franchise-network',
            'price' => 2999.00,
            'billing_interval' => 'monthly',
            'max_branches' => 5,
            'max_students' => 600,
            'features' => ['Multi-Branch (5)', 'Online MCQ Exams', 'QR Certificates', 'Printable Receipts', 'AI Retention Insights', 'Study Materials'],
            'is_active' => true,
        ]);

        $planEnterprise = SubscriptionPlan::create([
            'name' => 'Enterprise Multi-Franchise',
            'slug' => 'enterprise-multi-franchise',
            'price' => 7999.00,
            'billing_interval' => 'monthly',
            'max_branches' => 20,
            'max_students' => 3000,
            'features' => ['Unlimited Branches', 'Full API Access', 'Custom White-labeling', 'Dedicated Account Manager'],
            'is_active' => true,
        ]);

        // 2. Franchises
        $apexFranchise = Franchise::create([
            'subscription_plan_id' => $planGrowth->id,
            'name' => 'Apex Institute of Information Technology',
            'slug' => 'apex-institute',
            'code' => 'APEX01',
            'email' => 'contact@apextech.com',
            'phone' => '+91 98765 43210',
            'address' => 'Plot 42, Tech Park Avenue, Knowledge City',
            'city' => 'Pune',
            'state' => 'Maharashtra',
            'country' => 'India',
            'tax_number' => '27AAACA1234B1Z5',
            'status' => 'active',
            'plan_expires_at' => now()->addYear(),
            'max_students' => 600,
            'max_branches' => 5,
        ]);

        $nexgenFranchise = Franchise::create([
            'subscription_plan_id' => $planStarter->id,
            'name' => 'NexGen Coding & Robotics Academy',
            'slug' => 'nexgen-academy',
            'code' => 'NXG02',
            'email' => 'info@nexgencode.in',
            'phone' => '+91 91234 56789',
            'address' => 'Sector 18, Cyber Towers',
            'city' => 'Noida',
            'state' => 'Uttar Pradesh',
            'country' => 'India',
            'status' => 'active',
            'plan_expires_at' => now()->addMonths(6),
            'max_students' => 150,
            'max_branches' => 1,
        ]);

        // 3. Branches
        $branchDowntown = Branch::create([
            'franchise_id' => $apexFranchise->id,
            'name' => 'Downtown Central Campus',
            'code' => 'APEX-DT',
            'email' => 'downtown@apextech.com',
            'phone' => '+91 98765 43211',
            'address' => '2nd Floor, Apex Chambers, FC Road',
            'city' => 'Pune',
            'status' => 'active',
        ]);

        $branchSouth = Branch::create([
            'franchise_id' => $apexFranchise->id,
            'name' => 'South Campus Skill Hub',
            'code' => 'APEX-SC',
            'email' => 'south@apextech.com',
            'phone' => '+91 98765 43212',
            'address' => 'Building 4, Kothrud Main Square',
            'city' => 'Pune',
            'status' => 'active',
        ]);

        $branchNexGen = Branch::create([
            'franchise_id' => $nexgenFranchise->id,
            'name' => 'Cyber City Hub',
            'code' => 'NXG-CC',
            'email' => 'cybercity@nexgencode.in',
            'city' => 'Noida',
            'status' => 'active',
        ]);

        // 4. Users (RBAC)
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@remax.io',
            'phone' => '9876543200',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'active',
            'designation' => 'Chief Technology Officer',
        ]);

        $apexOwner = User::create([
            'franchise_id' => $apexFranchise->id,
            'name' => 'Vikram Singhania',
            'email' => 'owner@apextech.com',
            'phone' => '9876543210',
            'password' => Hash::make('password'),
            'role' => 'franchise_owner',
            'status' => 'active',
            'designation' => 'Managing Director & Franchisee',
        ]);

        $downtownAdmin = User::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'name' => 'Anand Verma',
            'email' => 'admin@apex-downtown.com',
            'phone' => '9876543211',
            'password' => Hash::make('password'),
            'role' => 'branch_admin',
            'status' => 'active',
            'designation' => 'Branch Center Head',
        ]);

        $trainer = User::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'name' => 'Rajesh Sharma',
            'email' => 'trainer@apextech.com',
            'phone' => '9876543212',
            'password' => Hash::make('password'),
            'role' => 'trainer',
            'status' => 'active',
            'designation' => 'Lead Software & Web Instructor',
        ]);

        $accountant = User::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'name' => 'Sunita Rao',
            'email' => 'accountant@apextech.com',
            'phone' => '9876543213',
            'password' => Hash::make('password'),
            'role' => 'accountant',
            'status' => 'active',
            'designation' => 'Senior Accounts Officer',
        ]);

        $studentUser = User::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'name' => 'Aarav Sharma',
            'email' => 'student@apextech.com',
            'phone' => '9876543214',
            'password' => Hash::make('password'),
            'role' => 'student',
            'status' => 'active',
        ]);

        $nexgenOwner = User::create([
            'franchise_id' => $nexgenFranchise->id,
            'name' => 'Neha Kapoor',
            'email' => 'owner@nexgen.com',
            'phone' => '9876543220',
            'password' => Hash::make('password'),
            'role' => 'franchise_owner',
            'status' => 'active',
        ]);

        // 5. Courses & Modules for Apex Institute
        $dcaCourse = Course::create([
            'franchise_id' => $apexFranchise->id,
            'name' => 'Diploma in Computer Applications (DCA)',
            'code' => 'DCA-101',
            'category' => 'Computer Basics & Office Automation',
            'type' => '6_months_diploma',
            'duration_weeks' => 24,
            'total_fee' => 12000.00,
            'registration_fee' => 500.00,
            'description' => 'Comprehensive diploma covering computer fundamentals, office productivity tools, business accounting with Tally, and foundational internet technologies.',
            'eligibility' => '10th / 12th Standard or Equivalent',
            'certificate_title' => 'Diploma in Computer Applications',
            'status' => 'active',
        ]);

        CourseModule::create([
            'franchise_id' => $apexFranchise->id,
            'course_id' => $dcaCourse->id,
            'title' => 'Module 1: Computer Fundamentals & Windows 11 OS',
            'description' => 'Hardware components, operating systems, file explorer, software utilities.',
            'order' => 1,
            'duration_hours' => 20,
        ]);

        CourseModule::create([
            'franchise_id' => $apexFranchise->id,
            'course_id' => $dcaCourse->id,
            'title' => 'Module 2: Microsoft Office Suite (Word, Excel, PowerPoint)',
            'description' => 'Document formatting, advanced Excel formulas, pivot tables, data presentation.',
            'order' => 2,
            'duration_hours' => 40,
        ]);

        CourseModule::create([
            'franchise_id' => $apexFranchise->id,
            'course_id' => $dcaCourse->id,
            'title' => 'Module 3: Financial Accounting with Tally Prime & GST',
            'description' => 'Ledger creation, vouchers, GST invoicing, balance sheet generation.',
            'order' => 3,
            'duration_hours' => 30,
        ]);

        $fullStackCourse = Course::create([
            'franchise_id' => $apexFranchise->id,
            'name' => 'Master Diploma in Full Stack Web Development',
            'code' => 'FSWD-201',
            'category' => 'Software Engineering',
            'type' => '1_year_diploma',
            'duration_weeks' => 48,
            'total_fee' => 28000.00,
            'registration_fee' => 1000.00,
            'description' => 'Professional engineering track covering HTML5, Tailwind CSS, modern JavaScript, PHP 8, Laravel Framework, MySQL architecture, and Git.',
            'eligibility' => 'Graduate / Diploma / IT Aspirant',
            'certificate_title' => 'Master Diploma in Full Stack Web Development',
            'status' => 'active',
        ]);

        // 6. Batches
        $dcaMorningBatch = Batch::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'course_id' => $dcaCourse->id,
            'trainer_id' => $trainer->id,
            'name' => 'DCA Morning Batch A',
            'batch_code' => 'DCA-2024-M1',
            'start_date' => Carbon::now()->subMonths(2),
            'end_date' => Carbon::now()->addMonths(4),
            'time_slot' => '09:00 AM - 11:00 AM (Mon - Fri)',
            'capacity' => 25,
            'status' => 'active',
        ]);

        $fswdEveningBatch = Batch::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'course_id' => $fullStackCourse->id,
            'trainer_id' => $trainer->id,
            'name' => 'Full Stack Evening Batch 1',
            'batch_code' => 'FSWD-2024-E1',
            'start_date' => Carbon::now()->subMonth(),
            'end_date' => Carbon::now()->addMonths(11),
            'time_slot' => '05:00 PM - 07:00 PM (Mon - Fri)',
            'capacity' => 30,
            'status' => 'active',
        ]);

        // 7. Students & Admissions
        $studentAarav = Student::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'user_id' => $studentUser->id,
            'admission_number' => 'ADM-2024-001',
            'student_id_code' => 'STU-1001',
            'first_name' => 'Aarav',
            'last_name' => 'Sharma',
            'date_of_birth' => '2003-05-14',
            'gender' => 'male',
            'email' => 'student@apextech.com',
            'phone' => '+91 98765 00001',
            'guardian_name' => 'Sanjay Sharma',
            'guardian_phone' => '+91 98765 00002',
            'address' => 'Flat 302, Green Valley Apartments, Shivajinagar, Pune',
            'qualification' => 'Undergraduate (Pursuing)',
            'admission_date' => Carbon::now()->subMonths(2),
            'status' => 'active',
        ]);

        $studentPriya = Student::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'admission_number' => 'ADM-2024-002',
            'student_id_code' => 'STU-1002',
            'first_name' => 'Priya',
            'last_name' => 'Patel',
            'date_of_birth' => '2002-11-20',
            'gender' => 'female',
            'email' => 'priya.patel@gmail.com',
            'phone' => '+91 98765 00003',
            'qualification' => 'Graduate',
            'admission_date' => Carbon::now()->subMonth(),
            'status' => 'active',
        ]);

        $studentRohan = Student::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'admission_number' => 'ADM-2024-003',
            'student_id_code' => 'STU-1003',
            'first_name' => 'Rohan',
            'last_name' => 'Verma',
            'gender' => 'male',
            'phone' => '+91 98765 00004',
            'admission_date' => Carbon::now()->subWeeks(3),
            'status' => 'active',
        ]);

        // 8. Enrollments
        $enrollmentAarav = Enrollment::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'student_id' => $studentAarav->id,
            'course_id' => $dcaCourse->id,
            'batch_id' => $dcaMorningBatch->id,
            'enrollment_date' => Carbon::now()->subMonths(2),
            'total_agreed_fee' => 12000.00,
            'discount_amount' => 1000.00,
            'final_fee' => 11000.00,
            'status' => 'enrolled',
        ]);

        $enrollmentPriya = Enrollment::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'student_id' => $studentPriya->id,
            'course_id' => $fullStackCourse->id,
            'batch_id' => $fswdEveningBatch->id,
            'enrollment_date' => Carbon::now()->subMonth(),
            'total_agreed_fee' => 28000.00,
            'discount_amount' => 2000.00,
            'final_fee' => 26000.00,
            'status' => 'enrolled',
        ]);

        // 9. Fee Invoices & Installments & Payments
        // Invoice for Aarav
        $invoiceAarav = FeeInvoice::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'student_id' => $studentAarav->id,
            'enrollment_id' => $enrollmentAarav->id,
            'invoice_number' => 'INV-2024-001',
            'title' => 'DCA Tuition & Lab Fee',
            'total_amount' => 11000.00,
            'paid_amount' => 6000.00,
            'pending_amount' => 5000.00,
            'due_date' => Carbon::now()->addMonth(),
            'status' => 'partially_paid',
        ]);

        $instAarav1 = FeeInstallment::create([
            'franchise_id' => $apexFranchise->id,
            'fee_invoice_id' => $invoiceAarav->id,
            'installment_number' => 1,
            'due_date' => Carbon::now()->subMonths(2),
            'amount' => 6000.00,
            'paid_amount' => 6000.00,
            'status' => 'paid',
        ]);

        $instAarav2 = FeeInstallment::create([
            'franchise_id' => $apexFranchise->id,
            'fee_invoice_id' => $invoiceAarav->id,
            'installment_number' => 2,
            'due_date' => Carbon::now()->addMonth(),
            'amount' => 5000.00,
            'paid_amount' => 0.00,
            'status' => 'pending',
        ]);

        Payment::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'student_id' => $studentAarav->id,
            'fee_invoice_id' => $invoiceAarav->id,
            'fee_installment_id' => $instAarav1->id,
            'receipt_number' => 'REC-2024-101',
            'amount' => 6000.00,
            'payment_method' => 'upi',
            'transaction_reference' => 'UPI/4123984712/HDFC',
            'payment_date' => Carbon::now()->subMonths(2),
            'received_by_user_id' => $accountant->id,
            'remarks' => 'First installment cleared via Google Pay',
        ]);

        // 10. Attendance Records for Aarav
        for ($i = 20; $i >= 1; $i--) {
            $date = Carbon::now()->subDays($i);
            if ($date->isWeekend()) continue;

            $status = ($i === 5) ? 'late' : (($i === 12) ? 'absent' : 'present');
            Attendance::create([
                'franchise_id' => $apexFranchise->id,
                'branch_id' => $branchDowntown->id,
                'batch_id' => $dcaMorningBatch->id,
                'student_id' => $studentAarav->id,
                'trainer_id' => $trainer->id,
                'date' => $date,
                'status' => $status,
                'remarks' => $status === 'late' ? '15 minutes late due to traffic' : null,
            ]);
        }

        // Today's attendance
        Attendance::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'batch_id' => $dcaMorningBatch->id,
            'student_id' => $studentAarav->id,
            'trainer_id' => $trainer->id,
            'date' => Carbon::today(),
            'status' => 'present',
        ]);

        // 11. Exams & Questions
        $exam = Exam::create([
            'franchise_id' => $apexFranchise->id,
            'course_id' => $dcaCourse->id,
            'batch_id' => $dcaMorningBatch->id,
            'title' => 'Module 1 & 2: Computer & Office Automation MCQ Test',
            'exam_type' => 'mcq',
            'total_marks' => 20,
            'passing_marks' => 8,
            'duration_minutes' => 30,
            'exam_date' => Carbon::now()->addDays(5),
            'status' => 'published',
            'instructions' => 'Each question carries 4 marks. There is no negative marking. Choose the best suitable option.',
        ]);

        ExamQuestion::create([
            'franchise_id' => $apexFranchise->id,
            'exam_id' => $exam->id,
            'question_text' => 'Which of the following is considered the primary brain of a computer system?',
            'question_type' => 'single_choice',
            'options' => [
                'A' => 'Hard Disk Drive (HDD)',
                'B' => 'Central Processing Unit (CPU)',
                'C' => 'Random Access Memory (RAM)',
                'D' => 'Read Only Memory (ROM)',
            ],
            'correct_answer' => 'B',
            'marks' => 4,
            'explanation' => 'The Central Processing Unit (CPU) executes software instructions and calculations.',
        ]);

        ExamQuestion::create([
            'franchise_id' => $apexFranchise->id,
            'exam_id' => $exam->id,
            'question_text' => 'In Microsoft Excel, which function is used to calculate the arithmetic average of a given range of cells?',
            'question_type' => 'single_choice',
            'options' => [
                'A' => '=SUM()',
                'B' => '=COUNT()',
                'C' => '=AVERAGE()',
                'D' => '=MEDIAN()',
            ],
            'correct_answer' => 'C',
            'marks' => 4,
            'explanation' => '=AVERAGE() computes the mathematical mean of numerical values in a range.',
        ]);

        ExamQuestion::create([
            'franchise_id' => $apexFranchise->id,
            'exam_id' => $exam->id,
            'question_text' => 'What is the default file extension for Microsoft Word 2016 and later versions?',
            'question_type' => 'single_choice',
            'options' => [
                'A' => '.doc',
                'B' => '.docx',
                'C' => '.txt',
                'D' => '.rtf',
            ],
            'correct_answer' => 'B',
            'marks' => 4,
            'explanation' => '.docx is the default XML-based document format for MS Word.',
        ]);

        ExamQuestion::create([
            'franchise_id' => $apexFranchise->id,
            'exam_id' => $exam->id,
            'question_text' => 'In financial accounting, what type of account represents the debts owed by the business to suppliers?',
            'question_type' => 'single_choice',
            'options' => [
                'A' => 'Current Asset',
                'B' => 'Sundry Debtors',
                'C' => 'Sundry Creditors (Current Liability)',
                'D' => 'Direct Expense',
            ],
            'correct_answer' => 'C',
            'marks' => 4,
            'explanation' => 'Sundry Creditors represent liabilities owed to vendors for credit purchases.',
        ]);

        ExamQuestion::create([
            'franchise_id' => $apexFranchise->id,
            'exam_id' => $exam->id,
            'question_text' => 'Which standard keyboard shortcut key is used in Windows to instantly save an active document?',
            'question_type' => 'single_choice',
            'options' => [
                'A' => 'Ctrl + P',
                'B' => 'Ctrl + S',
                'C' => 'Ctrl + Z',
                'D' => 'Alt + F4',
            ],
            'correct_answer' => 'B',
            'marks' => 4,
            'explanation' => 'Ctrl + S is the universal shortcut for saving files.',
        ]);

        // Seeded Result for Aarav
        ExamResult::create([
            'franchise_id' => $apexFranchise->id,
            'exam_id' => $exam->id,
            'student_id' => $studentAarav->id,
            'marks_obtained' => 16.00,
            'total_marks' => 20.00,
            'percentage' => 80.00,
            'grade' => 'A',
            'status' => 'pass',
            'feedback' => 'Commendable performance in computer basics and spreadsheet formulas.',
            'graded_by_user_id' => $trainer->id,
        ]);

        // 12. Certificates
        $certCode = 'd9b1c784-5f12-4a92-bf39-38e2195f1910';
        Certificate::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => $branchDowntown->id,
            'student_id' => $studentAarav->id,
            'course_id' => $dcaCourse->id,
            'certificate_number' => 'CERT-2024-APX1001',
            'verification_code' => $certCode,
            'issue_date' => Carbon::now()->subDays(5),
            'completion_date' => Carbon::now()->subDays(10),
            'grade' => 'Distinction',
            'percentage' => 88.50,
            'template_name' => 'classic_blue',
            'status' => 'issued',
        ]);

        // 13. Study Materials
        StudyMaterial::create([
            'franchise_id' => $apexFranchise->id,
            'course_id' => $dcaCourse->id,
            'batch_id' => $dcaMorningBatch->id,
            'uploader_user_id' => $trainer->id,
            'title' => 'Module 1 & 2 Quick Revision Notes & Shortcut Cheatsheet',
            'description' => 'Comprehensive study guide with MS Word & Excel keyboard shortcuts and Windows management commands.',
            'type' => 'pdf',
            'file_path' => null,
            'external_url' => null,
            'is_published' => true,
        ]);

        StudyMaterial::create([
            'franchise_id' => $apexFranchise->id,
            'course_id' => $dcaCourse->id,
            'batch_id' => $dcaMorningBatch->id,
            'uploader_user_id' => $trainer->id,
            'title' => 'Video Lecture: Advanced Excel VLOOKUP, HLOOKUP & Pivot Tables',
            'description' => 'Step-by-step video tutorial demonstrating nested conditional formatting and automated financial spreadsheets.',
            'type' => 'video_link',
            'external_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_published' => true,
        ]);

        // 14. Announcements
        Announcement::create([
            'franchise_id' => $apexFranchise->id,
            'branch_id' => null,
            'author_user_id' => $downtownAdmin->id,
            'title' => 'Institute Annual Technical Seminar & Guest Lecture on Friday',
            'content' => 'All morning and evening batch students are invited to attend the special masterclass on cloud technologies and industry career tracks this Friday at 11:30 AM in Auditorium Hall A.',
            'target_role' => 'all',
            'is_active' => true,
        ]);

        // 15. Audit Logs
        AuditLog::create([
            'franchise_id' => $apexFranchise->id,
            'user_id' => $apexOwner->id,
            'action' => 'branch_created',
            'model_type' => Branch::class,
            'model_id' => $branchDowntown->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'new_values' => ['name' => 'Downtown Central Campus', 'code' => 'APEX-DT'],
        ]);

        AuditLog::create([
            'franchise_id' => $apexFranchise->id,
            'user_id' => $accountant->id,
            'action' => 'payment_recorded',
            'model_type' => Payment::class,
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'new_values' => ['receipt_number' => 'REC-2024-101', 'amount' => 6000.00],
        ]);

        AuditLog::create([
            'franchise_id' => $apexFranchise->id,
            'user_id' => $downtownAdmin->id,
            'action' => 'certificate_issued',
            'model_type' => Certificate::class,
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'new_values' => ['certificate_number' => 'CERT-2024-APX1001', 'student' => 'Aarav Sharma'],
        ]);

        // 16. Default Platform System Settings
        \App\Models\SystemSetting::set('app_name', 'Remax');
        \App\Models\SystemSetting::set('app_tagline', 'Multi-Tenant Institute & Franchise Management SaaS');
        \App\Models\SystemSetting::set('support_email', 'admin@remax.io');
        \App\Models\SystemSetting::set('support_phone', '+91 98765 43200');
        \App\Models\SystemSetting::set('footer_text', 'Remax SaaS Platform. All rights reserved.');
        \App\Models\SystemSetting::set('hero_badge', 'Complete Multi-Tenant Architecture & Franchise Management');
    }
}
