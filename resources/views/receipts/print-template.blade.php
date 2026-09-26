<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt - {{ $payment->receipt_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .receipt-box { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4 flex flex-col items-center">

    <!-- Action Bar -->
    <div class="no-print max-w-2xl w-full mb-4 flex items-center justify-between bg-white px-6 py-3 rounded-xl shadow-sm border border-slate-200">
        <span class="text-xs text-slate-500">Official Payment Receipt &bull; {{ $payment->receipt_number }}</span>
        <button onclick="window.print()" class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition flex items-center space-x-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Print Receipt</span>
        </button>
    </div>

    <!-- Receipt Container -->
    <div class="receipt-box max-w-2xl w-full bg-white rounded-2xl shadow-xl border border-slate-200 p-8 sm:p-10 text-slate-800">
        <!-- Header -->
        <div class="flex items-start justify-between pb-6 border-b border-slate-200">
            <div>
                <h1 class="text-2xl font-black text-blue-900 tracking-tight">{{ $payment->franchise->name }}</h1>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">{{ $payment->branch->name }} Center</p>
                <p class="text-xs text-slate-400 mt-1">{{ $payment->branch->address ?? 'Central Academic Building' }}, {{ $payment->branch->city ?? 'Campus' }}</p>
                @if($payment->branch->phone)
                    <p class="text-xs text-slate-400">Tel: {{ $payment->branch->phone }}</p>
                @endif
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold uppercase rounded-md mb-2">
                    Payment Receipt
                </span>
                <p class="text-xs font-mono font-bold text-slate-900">#{{ $payment->receipt_number }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Date: {{ $payment->payment_date->format('d M, Y') }}</p>
            </div>
        </div>

        <!-- Student & Course Meta -->
        <div class="grid grid-cols-2 gap-4 py-6 border-b border-slate-200 text-xs">
            <div>
                <span class="text-slate-400 uppercase tracking-wider text-[10px] font-bold block">Billed To (Student)</span>
                <span class="text-base font-bold text-slate-900 block mt-0.5">{{ $payment->student->full_name }}</span>
                <span class="text-slate-500 font-mono">Roll: {{ $payment->student->student_id_code }}</span>
                <span class="text-slate-400 block mt-0.5">Adm #: {{ $payment->student->admission_number }}</span>
            </div>

            <div class="text-right">
                <span class="text-slate-400 uppercase tracking-wider text-[10px] font-bold block">Course Enrollment</span>
                <span class="text-sm font-bold text-slate-900 block mt-0.5">{{ $payment->feeInvoice->title }}</span>
                <span class="text-slate-500 font-mono block">Inv: {{ $payment->feeInvoice->invoice_number }}</span>
                <span class="inline-block mt-1 px-2 py-0.5 bg-blue-50 text-blue-700 font-semibold rounded text-[11px] uppercase">
                    Method: {{ strtoupper($payment->payment_method) }}
                </span>
            </div>
        </div>

        <!-- Payment Breakdown Table -->
        <div class="py-6 border-b border-slate-200">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 uppercase text-[10px] font-bold">
                        <th class="pb-2">Description</th>
                        <th class="pb-2">Reference</th>
                        <th class="pb-2 text-right">Amount (INR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-3 font-semibold text-slate-800">
                            Course Tuition Fee Installment
                            @if($payment->fee_installment_id)
                                <span class="text-slate-400 font-normal">(Installment #{{ $payment->feeInstallment?->installment_number }})</span>
                            @endif
                        </td>
                        <td class="py-3 text-slate-500 font-mono">
                            {{ $payment->transaction_reference ?? 'Direct Counter Settlement' }}
                        </td>
                        <td class="py-3 text-right font-mono font-bold text-slate-900 text-sm">
                            ₹{{ number_format($payment->amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Ledger Summary -->
        <div class="py-4 border-b border-slate-200 space-y-1.5 text-xs text-right">
            <div class="flex justify-between text-slate-500">
                <span>Total Agreed Course Fee:</span>
                <span class="font-mono">₹{{ number_format($payment->feeInvoice->total_amount, 2) }}</span>
            </div>
            <div class="flex justify-between text-slate-500">
                <span>Total Cumulative Amount Paid:</span>
                <span class="font-mono font-semibold text-emerald-600">₹{{ number_format($payment->feeInvoice->paid_amount, 2) }}</span>
            </div>
            <div class="flex justify-between text-slate-800 font-bold pt-1 border-t border-slate-100">
                <span>Current Amount Paid (This Receipt):</span>
                <span class="font-mono text-base text-blue-700">₹{{ number_format($payment->amount, 2) }}</span>
            </div>
            <div class="flex justify-between font-semibold {{ $payment->feeInvoice->pending_amount > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                <span>Outstanding Balance Remaining:</span>
                <span class="font-mono">₹{{ number_format($payment->feeInvoice->pending_amount, 2) }}</span>
            </div>
        </div>

        <!-- Footer & Signatures -->
        <div class="pt-8 flex items-end justify-between text-xs text-slate-500">
            <div>
                <p class="font-bold text-slate-700">Terms & Notes:</p>
                <p class="text-[11px] text-slate-400 max-w-xs mt-0.5">
                    Fees once paid are non-refundable. Please keep this receipt safely for fee clearance and examination admit cards.
                </p>
                <p class="text-[10px] text-slate-400 font-mono mt-2">Cashier: {{ $payment->receivedBy?->name ?? 'Accounts Desk' }}</p>
            </div>

            <div class="text-center">
                <div class="w-36 border-b border-slate-300 pb-1">
                    <span class="font-mono text-xs font-bold text-slate-700">[Authorized Stamp]</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider block mt-1">Authorized Cashier</span>
            </div>
        </div>
    </div>

</body>
</html>
