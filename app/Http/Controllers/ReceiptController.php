<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * Printable Fee Payment Receipt.
     */
    public function print(string $receiptNumber)
    {
        $payment = Payment::with([
            'student',
            'branch',
            'franchise',
            'feeInvoice.installments',
            'receivedBy'
        ])
        ->where('receipt_number', $receiptNumber)
        ->firstOrFail();

        return view('receipts.print-template', compact('payment'));
    }
}
