<?php

namespace App\Filament\Franchise\Resources\StudentResource\Pages;

use App\Filament\Franchise\Resources\StudentResource;
use App\Models\Batch;
use App\Models\FeeInvoice;
use App\Models\Student;
use App\Services\AiInsightService;
use App\Services\FeeService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print_id')
                ->label('Print ID Card')
                ->icon('heroicon-o-identification')
                ->color('info')
                ->url(fn(Student $record) => route('student.id-card.print', $record->id))
                ->openUrlInNewTab(),

            Actions\Action::make('transfer_batch')
                ->label('Shift Batch')
                ->icon('heroicon-o-arrows-right-left')
                ->color('warning')
                ->visible(fn() => auth()->user() && !auth()->user()->isTrainer() && !auth()->user()->isAccountant())
                ->form([
                    Forms\Components\Select::make('batch_id')
                        ->label('Select Destination Batch')
                        ->options(fn(Student $record) => Batch::where('franchise_id', $record->franchise_id)->where('status', 'running')->pluck('name', 'id'))
                        ->required(),
                    Forms\Components\Textarea::make('transfer_reason')
                        ->label('Reason for Transfer')
                        ->required(),
                ])
                ->action(function (Student $record, array $data) {
                    $enrollment = $record->enrollments()->latest()->first();
                    if ($enrollment) {
                        $oldBatch = $enrollment->batch?->name ?? 'Unassigned';
                        $enrollment->update(['batch_id' => $data['batch_id']]);
                        $newBatch = Batch::find($data['batch_id'])?->name ?? 'New Batch';
                        $record->notes = trim(($record->notes ?? '') . "\n[" . now()->format('Y-m-d H:i') . "] Transferred from {$oldBatch} to {$newBatch}. Reason: {$data['transfer_reason']}");
                        $record->save();

                        Notification::make()->title('Batch Transferred')->body("Student shifted to {$newBatch}")->success()->send();
                    } else {
                        Notification::make()->title('No active enrollment found')->danger()->send();
                    }
                }),

            Actions\Action::make('collect_fee')
                ->label('Collect Fee')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->visible(fn() => auth()->user() && !auth()->user()->isTrainer())
                ->form([
                    Forms\Components\Select::make('fee_invoice_id')
                        ->label('Select Invoice')
                        ->options(fn(Student $record) => $record->feeInvoices()->where('status', '!=', 'paid')->pluck('title', 'id'))
                        ->required(),
                    Forms\Components\TextInput::make('amount')
                        ->label('Payment Amount')
                        ->numeric()
                        ->prefix('₹')
                        ->required(),
                    Forms\Components\Select::make('payment_method')
                        ->options([
                            'cash' => 'Cash',
                            'upi' => 'UPI (GPay / PhonePe / Paytm)',
                            'bank_transfer' => 'Bank Transfer / NEFT',
                            'online' => 'Online Gateway',
                            'cheque' => 'Cheque',
                            'card' => 'Debit/Credit Card',
                        ])
                        ->default('cash')
                        ->required(),
                    Forms\Components\TextInput::make('transaction_reference')
                        ->placeholder('e.g. UPI Ref / Cheque No.'),
                    Forms\Components\Textarea::make('remarks')
                        ->placeholder('Optional notes...'),
                ])
                ->action(function (Student $record, array $data) {
                    $invoice = FeeInvoice::find($data['fee_invoice_id']);
                    if (!$invoice) {
                        Notification::make()->title('Invoice not found')->danger()->send();
                        return;
                    }

                    $feeService = app(FeeService::class);
                    $payment = $feeService->recordPayment(
                        invoice: $invoice,
                        amount: (float)$data['amount'],
                        paymentMethod: $data['payment_method'],
                        transactionReference: $data['transaction_reference'] ?? null,
                        remarks: $data['remarks'] ?? null
                    );

                    Notification::make()
                        ->title('Payment Recorded')
                        ->body("Receipt #{$payment->receipt_number} generated for ₹{$payment->amount}")
                        ->success()
                        ->send();
                }),

            Actions\Action::make('ai_risk')
                ->label('AI Retention Insights')
                ->icon('heroicon-o-sparkles')
                ->color('primary')
                ->modalHeading('AI Student Performance & Retention Analysis')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalContent(function (Student $record) {
                    $insightService = app(AiInsightService::class);
                    $insight = $insightService->assessStudentRisk($record);
                    return view('filament.modals.ai-risk-modal', ['insight' => $insight, 'student' => $record]);
                }),

            Actions\EditAction::make(),
        ];
    }
}
