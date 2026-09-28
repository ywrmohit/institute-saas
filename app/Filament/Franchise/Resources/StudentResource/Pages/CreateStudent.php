<?php

namespace App\Filament\Franchise\Resources\StudentResource\Pages;

use App\Filament\Franchise\Resources\StudentResource;
use App\Models\Enrollment;
use App\Models\FeeInvoice;
use App\Models\User;
use App\Services\FeeService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected array $admissionData = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->admissionData = [
            'enrolled_course_id' => $data['enrolled_course_id'] ?? null,
            'enrolled_batch_id' => $data['enrolled_batch_id'] ?? null,
            'course_fee' => $data['course_fee'] ?? 0,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'net_fee' => $data['net_fee'] ?? 0,
            'installments_count' => $data['installments_count'] ?? 1,
            'auto_create_portal_user' => $data['auto_create_portal_user'] ?? false,
        ];

        unset(
            $data['enrolled_course_id'],
            $data['enrolled_batch_id'],
            $data['course_fee'],
            $data['discount_amount'],
            $data['net_fee'],
            $data['installments_count'],
            $data['auto_create_portal_user']
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        $student = $this->getRecord();
        $formData = $this->admissionData;

        // 1. Automated Course Enrollment & Fee Setup
        $courseFee = (float)($formData['course_fee'] ?? 0);
        $discount = (float)($formData['discount_amount'] ?? 0);
        $netFee = (float)($formData['net_fee'] ?? max(0, $courseFee - $discount));

        if (!empty($formData['enrolled_course_id'])) {
            $enrollment = Enrollment::create([
                'franchise_id' => $student->franchise_id,
                'branch_id' => $student->branch_id,
                'student_id' => $student->id,
                'course_id' => $formData['enrolled_course_id'],
                'batch_id' => $formData['enrolled_batch_id'] ?? null,
                'enrollment_date' => $student->admission_date ?? now(),
                'total_agreed_fee' => $courseFee,
                'discount_amount' => $discount,
                'final_fee' => $netFee,
                'status' => 'active',
            ]);

            // 2. Automated Fee Invoice & Installment Plan via FeeService
            if ($netFee > 0) {
                $installmentsCount = (int)($formData['installments_count'] ?? 1);
                $feeService = app(FeeService::class);
                $feeService->createInvoiceForEnrollment($enrollment, $installmentsCount, now()->addDays(15));
            }
        }

        // 3. Automated Student Portal Account
        if (!empty($formData['auto_create_portal_user'])) {
            $email = $student->email ?: strtolower($student->student_id_code) . '@student.remax.io';
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $student->full_name,
                    'password' => Hash::make('student123'),
                    'phone' => $student->phone,
                    'role' => 'student',
                    'status' => 'active',
                    'franchise_id' => $student->franchise_id,
                    'branch_id' => $student->branch_id,
                ]
            );

            $student->update(['user_id' => $user->id, 'email' => $email]);

            Notification::make()
                ->title('Admission Complete & Portal Ready')
                ->body("Portal login: {$email} (Default password: student123)")
                ->success()
                ->persistent()
                ->send();
        }
    }
}
