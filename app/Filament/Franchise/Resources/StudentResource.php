<?php

namespace App\Filament\Franchise\Resources;

use App\Filament\Franchise\Resources\StudentResource\Pages;
use App\Models\Batch;
use App\Models\Course;
use App\Models\FeeInvoice;
use App\Models\Student;
use App\Services\AiInsightService;
use App\Services\FeeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Student Admissions';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Student Profile')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Personal Details')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->image()
                                    ->directory('student-photos')
                                    ->avatar()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('admission_number')
                                    ->label('Admission No.')
                                    ->required()
                                    ->default(fn() => 'ADM-' . date('Y') . '-' . rand(1000, 9999))
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('student_id_code')
                                    ->label('Student ID / Roll Code')
                                    ->required()
                                    ->default(fn() => 'STU-' . strtoupper(Str::random(6)))
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('last_name')
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->label('Date of Birth'),
                                Forms\Components\Select::make('gender')
                                    ->options([
                                        'male' => 'Male',
                                        'female' => 'Female',
                                        'other' => 'Other',
                                    ])
                                    ->default('male')
                                    ->required(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Contact & Guardians')
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->required()
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('guardian_name')
                                    ->label('Parent / Guardian Name')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('guardian_phone')
                                    ->label('Guardian Contact No.')
                                    ->tel()
                                    ->maxLength(20),
                                Forms\Components\Textarea::make('address')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Academic & Enrollment')
                            ->schema([
                                Forms\Components\Select::make('branch_id')
                                    ->relationship('branch', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\DatePicker::make('admission_date')
                                    ->default(today())
                                    ->required(),
                                Forms\Components\Select::make('qualification')
                                    ->options([
                                        'High School (10th)' => 'High School (10th)',
                                        'Intermediate (12th)' => 'Intermediate (12th)',
                                        'Undergraduate (Pursuing)' => 'Undergraduate (Pursuing)',
                                        'Graduate' => 'Graduate',
                                        'Postgraduate' => 'Postgraduate',
                                        'Working Professional' => 'Working Professional',
                                    ])
                                    ->searchable(),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'completed' => 'Graduated / Completed',
                                        'dropped' => 'Dropped Out',
                                        'suspended' => 'Suspended',
                                    ])
                                    ->default('active')
                                    ->required(),
                                Forms\Components\Textarea::make('notes')
                                    ->placeholder('Any special remarks or background notes...')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->defaultImageUrl(url('/default-avatar.png')),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name'])
                    ->weight('bold')
                    ->description(fn(Student $record): string => "ID: {$record->student_id_code} | Adm: {$record->admission_number}"),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->icon('heroicon-m-phone'),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('enrollments.course.name')
                    ->label('Enrolled Course')
                    ->badge()
                    ->color('info')
                    ->default('Not Enrolled'),
                Tables\Columns\TextColumn::make('attendance_pct')
                    ->label('Attendance')
                    ->state(fn(Student $record): string => $record->calculateAttendancePercentage() . '%')
                    ->badge()
                    ->color(fn(Student $record): string => match (true) {
                        $record->calculateAttendancePercentage() >= 75 => 'success',
                        $record->calculateAttendancePercentage() >= 60 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'primary',
                        'dropped' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('admission_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->relationship('branch', 'name')
                    ->label('Branch'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'dropped' => 'Dropped',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('ai_risk')
                    ->label('AI Insights')
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

                Tables\Actions\Action::make('collect_fee')
                    ->label('Fee')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
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

                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
