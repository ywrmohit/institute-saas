<?php

namespace App\Filament\Franchise\Resources;

use App\Filament\Franchise\Resources\FeeInvoiceResource\Pages;
use App\Models\FeeInvoice;
use App\Models\Student;
use App\Services\FeeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeeInvoiceResource extends Resource
{
    protected static ?string $model = FeeInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Financials';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Fee Invoice Information')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->options(fn() => Student::all()->pluck('full_name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('branch_id')
                            ->relationship('branch', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('invoice_number')
                            ->required()
                            ->maxLength(50)
                            ->default(fn() => 'INV-' . strtoupper(\Illuminate\Support\Str::random(8))),
                        Forms\Components\TextInput::make('title')
                            ->default('Tuition & Course Fee')
                            ->required(),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Invoice Amount')
                            ->numeric()
                            ->prefix('₹')
                            ->required(),
                        Forms\Components\TextInput::make('paid_amount')
                            ->numeric()
                            ->prefix('₹')
                            ->default(0)
                            ->readOnly(),
                        Forms\Components\TextInput::make('pending_amount')
                            ->numeric()
                            ->prefix('₹')
                            ->readOnly(),
                        Forms\Components\DatePicker::make('due_date')
                            ->default(today()->addMonth()),
                        Forms\Components\Select::make('status')
                            ->options([
                                'unpaid' => 'Unpaid',
                                'partially_paid' => 'Partially Paid',
                                'paid' => 'Fully Paid',
                                'overdue' => 'Overdue',
                            ])
                            ->default('unpaid')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Payment Schedule & Installments')
                    ->schema([
                        Forms\Components\Repeater::make('installments')
                            ->relationship('installments')
                            ->schema([
                                Forms\Components\TextInput::make('installment_number')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\DatePicker::make('due_date')
                                    ->required(),
                                Forms\Components\TextInput::make('amount')
                                    ->numeric()
                                    ->prefix('₹')
                                    ->required(),
                                Forms\Components\TextInput::make('paid_amount')
                                    ->numeric()
                                    ->prefix('₹')
                                    ->default(0),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'overdue' => 'Overdue',
                                    ])
                                    ->default('pending')
                                    ->required(),
                            ])
                            ->columns(5)
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->description(fn(FeeInvoice $record): string => "Roll: {$record->student?->student_id_code}"),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Paid')
                    ->money('INR')
                    ->sortable()
                    ->color('success'),
                Tables\Columns\TextColumn::make('pending_amount')
                    ->label('Pending')
                    ->money('INR')
                    ->sortable()
                    ->color(fn(FeeInvoice $record): string => $record->pending_amount > 0 ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'partially_paid' => 'warning',
                        'overdue' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'partially_paid' => 'Partially Paid',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                    ]),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->relationship('branch', 'name')
                    ->label('Branch'),
            ])
            ->actions([
                Tables\Actions\Action::make('record_payment')
                    ->label('Collect Payment')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->visible(fn(FeeInvoice $record): bool => $record->pending_amount > 0)
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Payment Amount')
                            ->numeric()
                            ->prefix('₹')
                            ->default(fn(FeeInvoice $record) => $record->pending_amount)
                            ->required(),
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'upi' => 'UPI (Google Pay / PhonePe / QR)',
                                'bank_transfer' => 'Bank Transfer / NEFT / IMPS',
                                'online' => 'Online Gateway (Razorpay/Card)',
                                'cheque' => 'Cheque',
                                'card' => 'Debit/Credit Card POS',
                            ])
                            ->default('cash')
                            ->required(),
                        Forms\Components\TextInput::make('transaction_reference')
                            ->placeholder('e.g. UPI Ref / Bank TXN / Cheque #'),
                        Forms\Components\Textarea::make('remarks')
                            ->placeholder('Optional notes...'),
                    ])
                    ->action(function (FeeInvoice $record, array $data) {
                        $feeService = app(FeeService::class);
                        $payment = $feeService->recordPayment(
                            invoice: $record,
                            amount: (float)$data['amount'],
                            paymentMethod: $data['payment_method'],
                            transactionReference: $data['transaction_reference'] ?? null,
                            remarks: $data['remarks'] ?? null
                        );

                        Notification::make()
                            ->title('Payment Succeeded')
                            ->body("Receipt #{$payment->receipt_number} issued for ₹{$payment->amount}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeInvoices::route('/'),
            'create' => Pages\CreateFeeInvoice::route('/create'),
            'edit' => Pages\EditFeeInvoice::route('/{record}/edit'),
        ];
    }
}
