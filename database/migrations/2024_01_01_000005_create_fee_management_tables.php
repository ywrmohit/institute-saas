<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Fee Invoices
        Schema::create('fee_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('enrollment_id')->nullable()->constrained('enrollments')->nullOnDelete();
            $table->string('invoice_number');
            $table->string('title')->default('Course Tuition Fee');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('pending_amount', 10, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('status')->default('unpaid'); // unpaid, partially_paid, paid, overdue
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['franchise_id', 'invoice_number']);
        });

        // 2. Fee Installments
        Schema::create('fee_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->cascadeOnDelete();
            $table->foreignId('fee_invoice_id')->constrained('fee_invoices')->cascadeOnDelete();
            $table->unsignedInteger('installment_number');
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('status')->default('pending'); // pending, paid, overdue
            $table->timestamps();
        });

        // 3. Payments / Receipts
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('fee_invoice_id')->constrained('fee_invoices')->cascadeOnDelete();
            $table->foreignId('fee_installment_id')->nullable()->constrained('fee_installments')->nullOnDelete();
            $table->string('receipt_number');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->default('cash'); // cash, upi, bank_transfer, online, cheque, card
            $table->string('transaction_reference')->nullable();
            $table->date('payment_date');
            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['franchise_id', 'receipt_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('fee_installments');
        Schema::dropIfExists('fee_invoices');
    }
};
