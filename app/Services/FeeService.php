<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\FeeInstallment;
use App\Models\FeeInvoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Str;

class FeeService
{
    /**
     * Create an invoice for an enrollment with installment breakdown.
     */
    public function createInvoiceForEnrollment(
        Enrollment $enrollment,
        int $numberOfInstallments = 1,
        ?Carbon $firstDueDate = null
    ): FeeInvoice {
        $firstDueDate = $firstDueDate ?? Carbon::today();
        $invoiceNumber = 'INV-' . strtoupper(Str::random(8));

        $invoice = FeeInvoice::create([
            'franchise_id' => $enrollment->franchise_id,
            'branch_id' => $enrollment->branch_id,
            'student_id' => $enrollment->student_id,
            'enrollment_id' => $enrollment->id,
            'invoice_number' => $invoiceNumber,
            'title' => "Tuition Fee - " . ($enrollment->course?->name ?? 'Course'),
            'total_amount' => $enrollment->final_fee,
            'paid_amount' => 0,
            'pending_amount' => $enrollment->final_fee,
            'due_date' => $firstDueDate,
            'status' => 'unpaid',
        ]);

        $installmentAmount = round($enrollment->final_fee / max(1, $numberOfInstallments), 2);
        $totalAllocated = 0;

        for ($i = 1; $i <= $numberOfInstallments; $i++) {
            $isLast = ($i === $numberOfInstallments);
            $amount = $isLast ? ($enrollment->final_fee - $totalAllocated) : $installmentAmount;
            $totalAllocated += $amount;

            $dueDate = (clone $firstDueDate)->addMonths($i - 1);

            FeeInstallment::create([
                'franchise_id' => $enrollment->franchise_id,
                'fee_invoice_id' => $invoice->id,
                'installment_number' => $i,
                'due_date' => $dueDate,
                'amount' => $amount,
                'late_fee' => 0,
                'paid_amount' => 0,
                'status' => 'pending',
            ]);
        }

        AuditLog::log('invoice_created', $invoice, null, $invoice->toArray(), $enrollment->franchise_id);

        return $invoice;
    }

    /**
     * Record a payment against an invoice and optionally an installment.
     */
    public function recordPayment(
        FeeInvoice $invoice,
        float $amount,
        string $paymentMethod = 'cash',
        ?string $transactionReference = null,
        ?int $installmentId = null,
        ?string $remarks = null,
        ?Carbon $paymentDate = null
    ): Payment {
        $paymentDate = $paymentDate ?? Carbon::today();
        $receiptNumber = 'REC-' . strtoupper(Str::random(8));

        $payment = Payment::create([
            'franchise_id' => $invoice->franchise_id,
            'branch_id' => $invoice->branch_id,
            'student_id' => $invoice->student_id,
            'fee_invoice_id' => $invoice->id,
            'fee_installment_id' => $installmentId,
            'receipt_number' => $receiptNumber,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'transaction_reference' => $transactionReference,
            'payment_date' => $paymentDate,
            'received_by_user_id' => auth()->id(),
            'remarks' => $remarks,
        ]);

        // If specific installment was targeted, update installment paid amount
        if ($installmentId) {
            $installment = FeeInstallment::find($installmentId);
            if ($installment) {
                $installment->paid_amount += $amount;
                if ($installment->paid_amount >= $installment->amount) {
                    $installment->status = 'paid';
                }
                $installment->save();
            }
        } else {
            // Auto allocate to oldest unpaid installment
            $unpaidInstallments = $invoice->installments()->where('status', '!=', 'paid')->orderBy('installment_number')->get();
            $remaining = $amount;
            foreach ($unpaidInstallments as $inst) {
                $deficit = $inst->amount - $inst->paid_amount;
                if ($deficit > 0) {
                    $allocate = min($remaining, $deficit);
                    $inst->paid_amount += $allocate;
                    $remaining -= $allocate;
                    if ($inst->paid_amount >= $inst->amount) {
                        $inst->status = 'paid';
                    }
                    $inst->save();
                }
                if ($remaining <= 0) break;
            }
        }

        $invoice->recalculateBalances();

        AuditLog::log('payment_recorded', $payment, null, $payment->toArray(), $invoice->franchise_id);

        return $payment;
    }
}
