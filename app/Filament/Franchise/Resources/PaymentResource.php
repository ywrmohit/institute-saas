<?php

namespace App\Filament\Franchise\Resources;

use App\Filament\Franchise\Resources\PaymentResource\Pages;
use App\Models\FeeInvoice;
use App\Models\Payment;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Financials';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('view_payments'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('record_payments'));
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('record_payments'));
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('record_payments'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->isBranchAdmin() && auth()->user()->branch_id) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment & Receipt Details')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->options(fn() => Student::all()->pluck('full_name', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive(),
                        Forms\Components\Select::make('fee_invoice_id')
                            ->label('Fee Invoice')
                            ->options(function (Forms\Get $get) {
                                $studentId = $get('student_id');
                                if (!$studentId) return FeeInvoice::pluck('title', 'id');
                                return FeeInvoice::where('student_id', $studentId)->pluck('title', 'id');
                            })
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('branch_id')
                            ->relationship('branch', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('receipt_number')
                            ->required()
                            ->default(fn() => 'REC-' . strtoupper(\Illuminate\Support\Str::random(8))),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('₹')
                            ->required(),
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'upi' => 'UPI (Google Pay / PhonePe / Paytm)',
                                'bank_transfer' => 'Bank Transfer / NEFT / IMPS',
                                'online' => 'Online Gateway',
                                'cheque' => 'Cheque',
                                'card' => 'Debit/Credit Card',
                            ])
                            ->default('cash')
                            ->required(),
                        Forms\Components\TextInput::make('transaction_reference')
                            ->placeholder('e.g. UTR / Transaction ID / Cheque #'),
                        Forms\Components\DatePicker::make('payment_date')
                            ->default(today())
                            ->required(),
                        Forms\Components\Textarea::make('remarks')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label('Receipt #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('INR')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn(string $state): string => strtoupper($state)),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('receivedBy.name')
                    ->label('Cashier / Staff')
                    ->default('System')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('payment_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'upi' => 'UPI',
                        'bank_transfer' => 'Bank Transfer',
                        'online' => 'Online',
                    ]),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->relationship('branch', 'name')
                    ->label('Branch'),
                Tables\Filters\Filter::make('payment_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Payment Date From'),
                        Forms\Components\DatePicker::make('until')->label('Payment Date Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $d) => $q->whereDate('payment_date', '>=', $d))
                            ->when($data['until'], fn($q, $d) => $q->whereDate('payment_date', '<=', $d));
                    }),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\Action::make('print_receipt')
                    ->label('Print Receipt')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->url(fn(Payment $record): string => route('receipt.print', ['receiptNumber' => $record->receipt_number]))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
