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
    protected static ?string $navigationGroup = 'Student Lifecycle';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('view_students'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('create_students'));
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('edit_students'));
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('delete_students'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->isBranchAdmin() && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($user?->isTrainer()) {
            $query->whereHas('enrollments.batch', fn($q) => $q->where('trainer_id', $user->id));
        }

        return $query;
    }

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
                                    ->disk('public')
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

                                Forms\Components\Section::make('Course Enrollment & Initial Fee Setup')
                                    ->description('One-step automated enrollment and fee schedule creation upon saving')
                                    ->schema([
                                        Forms\Components\Select::make('enrolled_course_id')
                                            ->label('Select Course')
                                            ->options(fn() => Course::where('status', 'active')->pluck('name', 'id'))
                                            ->searchable()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                if ($course = Course::find($state)) {
                                                    $set('course_fee', $course->fee);
                                                    $set('net_fee', $course->fee);
                                                }
                                            }),

                                        Forms\Components\Select::make('enrolled_batch_id')
                                            ->label('Select Batch / Timing')
                                            ->options(function (Forms\Get $get) {
                                                $courseId = $get('enrolled_course_id');
                                                if (!$courseId) return Batch::where('status', 'running')->pluck('name', 'id');
                                                return Batch::where('course_id', $courseId)->pluck('name', 'id');
                                            })
                                            ->searchable(),

                                        Forms\Components\TextInput::make('course_fee')
                                            ->label('Course Fee (₹)')
                                            ->numeric()
                                            ->prefix('₹')
                                            ->reactive()
                                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                                $fee = (float)$get('course_fee');
                                                $discount = (float)$get('discount_amount');
                                                $set('net_fee', max(0, $fee - $discount));
                                            }),

                                        Forms\Components\TextInput::make('discount_amount')
                                            ->label('Scholarship / Discount (₹)')
                                            ->numeric()
                                            ->prefix('₹')
                                            ->default(0)
                                            ->reactive()
                                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                                $fee = (float)$get('course_fee');
                                                $discount = (float)$get('discount_amount');
                                                $set('net_fee', max(0, $fee - $discount));
                                            }),

                                        Forms\Components\TextInput::make('net_fee')
                                            ->label('Net Payable Fee (₹)')
                                            ->numeric()
                                            ->prefix('₹')
                                            ->readOnly(),

                                        Forms\Components\Select::make('installments_count')
                                            ->label('Fee Payment Plan')
                                            ->options([
                                                1 => 'Full Payment (Single Installment)',
                                                2 => '2 Installments (50% / 50%)',
                                                3 => '3 Installments',
                                                4 => '4 Quarterly Installments',
                                                6 => '6 Monthly Installments',
                                                12 => '12 Monthly Installments',
                                            ])
                                            ->default(1),

                                        Forms\Components\Checkbox::make('auto_create_portal_user')
                                            ->label('Auto-Generate Student Portal Login (Default password: student123)')
                                            ->default(true),
                                    ])->columns(2)->columnSpanFull(),

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
                    ->disk('public')
                    ->defaultImageUrl(fn(Student $record): string => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&background=0284c7&color=fff'),
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
                    ->label('Branch')
                    ->preload(),
                Tables\Filters\SelectFilter::make('course')
                    ->relationship('enrollments.course', 'name')
                    ->label('Course')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed / Graduated',
                        'dropped' => 'Dropped Out',
                        'suspended' => 'Suspended',
                    ]),
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ]),
                Tables\Filters\Filter::make('fee_status')
                    ->form([
                        Forms\Components\Select::make('fee_condition')
                            ->label('Fee Balance')
                            ->options([
                                'due' => 'Has Outstanding Due (Defaulters)',
                                'paid' => 'Zero Due (Cleared)',
                            ]),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query->when(
                            ($data['fee_condition'] ?? null) === 'due',
                            fn($q) => $q->whereHas('feeInvoices', fn($iq) => $iq->where('pending_amount', '>', 0))
                        )->when(
                            ($data['fee_condition'] ?? null) === 'paid',
                            fn($q) => $q->whereDoesntHave('feeInvoices', fn($iq) => $iq->where('pending_amount', '>', 0))
                        );
                    }),
                Tables\Filters\Filter::make('admission_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Admitted From'),
                        Forms\Components\DatePicker::make('until')->label('Admitted Until'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when($data['from'] ?? null, fn($q, $d) => $q->whereDate('admission_date', '>=', $d))
                            ->when($data['until'] ?? null, fn($q, $d) => $q->whereDate('admission_date', '<=', $d));
                    }),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\Action::make('print_id')
                    ->label('ID Card')
                    ->icon('heroicon-o-identification')
                    ->color('info')
                    ->visible(fn() => auth()->user()?->can('print_student_id') ?? true)
                    ->url(fn(Student $record) => route('student.id-card.print', $record->id))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('transfer_batch')
                    ->label('Shift Batch')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('warning')
                    ->visible(fn() => auth()->user()?->can('transfer_students') ?? false)
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
                    ->visible(fn() => auth()->user()?->can('record_payments') ?? false)
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Student Profile Summary')
                    ->schema([
                        Infolists\Components\ImageEntry::make('photo')
                            ->circular()
                            ->disk('public')
                            ->defaultImageUrl(fn(Student $record): string => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&background=0284c7&color=fff'),
                        Infolists\Components\TextEntry::make('full_name')
                            ->label('Student Name')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('student_id_code')
                            ->label('Roll / ID Code')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('admission_number')
                            ->label('Admission No.'),
                        Infolists\Components\TextEntry::make('branch.name')
                            ->badge()
                            ->color('gray'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'active' => 'success',
                                'completed' => 'primary',
                                'dropped' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('attendance_pct')
                            ->label('Attendance Rate')
                            ->state(fn(Student $record): string => $record->calculateAttendancePercentage() . '%')
                            ->badge()
                            ->color(fn(Student $record): string => match (true) {
                                $record->calculateAttendancePercentage() >= 75 => 'success',
                                $record->calculateAttendancePercentage() >= 60 => 'warning',
                                default => 'danger',
                            }),
                    ])->columns(4),

                Infolists\Components\Tabs::make('Student Complete Dossier')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('Academic & Batch')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Infolists\Components\TextEntry::make('enrollments.course.name')
                                    ->label('Enrolled Course')
                                    ->badge()
                                    ->color('info')
                                    ->default('No Active Enrollment'),
                                Infolists\Components\TextEntry::make('enrollments.batch.name')
                                    ->label('Current Batch')
                                    ->badge()
                                    ->color('primary')
                                    ->default('Unassigned'),
                                Infolists\Components\TextEntry::make('enrollments.batch.timing')
                                    ->label('Batch Timing')
                                    ->icon('heroicon-m-clock')
                                    ->default('N/A'),
                                Infolists\Components\TextEntry::make('enrollments.batch.trainer.name')
                                    ->label('Assigned Faculty / Trainer')
                                    ->icon('heroicon-m-user')
                                    ->default('Not Assigned'),
                                Infolists\Components\TextEntry::make('qualification')
                                    ->label('Academic Background'),
                                Infolists\Components\TextEntry::make('admission_date')
                                    ->date()
                                    ->label('Date of Admission'),
                                Infolists\Components\TextEntry::make('notes')
                                    ->columnSpanFull()
                                    ->placeholder('No remarks recorded'),
                            ])->columns(3),

                        Infolists\Components\Tabs\Tab::make('Financial Ledger')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Infolists\Components\TextEntry::make('fee_summary')
                                    ->label('Financial Overview')
                                    ->state(function (Student $record) {
                                        $total = $record->feeInvoices()->sum('total_amount');
                                        $paid = $record->feeInvoices()->sum('paid_amount');
                                        $due = $record->feeInvoices()->sum('pending_amount');
                                        return "Total Fees: ₹{$total} | Total Paid: ₹{$paid} | Balance Due: ₹{$due}";
                                    })
                                    ->badge()
                                    ->color('warning')
                                    ->columnSpanFull(),
                                Infolists\Components\RepeatableEntry::make('feeInvoices')
                                    ->label('Invoices & Dues')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('invoice_number')->label('Invoice #'),
                                        Infolists\Components\TextEntry::make('title')->label('Fee Description'),
                                        Infolists\Components\TextEntry::make('total_amount')->label('Total (₹)'),
                                        Infolists\Components\TextEntry::make('paid_amount')->label('Paid (₹)'),
                                        Infolists\Components\TextEntry::make('pending_amount')->label('Due (₹)'),
                                        Infolists\Components\TextEntry::make('status')->badge()->color(fn($state) => match($state) {
                                            'paid' => 'success',
                                            'partially_paid' => 'warning',
                                            default => 'danger',
                                        }),
                                    ])->columns(6)->columnSpanFull(),
                            ]),

                        Infolists\Components\Tabs\Tab::make('Exams & Grading')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('examResults')
                                    ->label('Evaluation & Tests')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('exam.title')->label('Exam'),
                                        Infolists\Components\TextEntry::make('marks_obtained')->label('Score'),
                                        Infolists\Components\TextEntry::make('total_marks')->label('Max Marks'),
                                        Infolists\Components\TextEntry::make('percentage')->label('%')->suffix('%'),
                                        Infolists\Components\TextEntry::make('grade')->label('Grade')->badge(),
                                        Infolists\Components\TextEntry::make('status')->badge()->color(fn($state) => $state === 'passed' ? 'success' : 'danger'),
                                    ])->columns(6)->columnSpanFull(),
                            ]),

                        Infolists\Components\Tabs\Tab::make('Certifications')
                            ->icon('heroicon-o-trophy')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('certificates')
                                    ->label('Issued Credentials')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('certificate_number')->label('Certificate #'),
                                        Infolists\Components\TextEntry::make('verification_code')->label('Verification Code')->copyable(),
                                        Infolists\Components\TextEntry::make('course.name')->label('Course'),
                                        Infolists\Components\TextEntry::make('grade')->label('Grade'),
                                        Infolists\Components\TextEntry::make('issue_date')->date()->label('Issued On'),
                                    ])->columns(5)->columnSpanFull(),
                            ]),

                        Infolists\Components\Tabs\Tab::make('Contact & Guardians')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Infolists\Components\TextEntry::make('phone')->icon('heroicon-m-phone'),
                                Infolists\Components\TextEntry::make('email')->icon('heroicon-m-envelope'),
                                Infolists\Components\TextEntry::make('date_of_birth')->date(),
                                Infolists\Components\TextEntry::make('gender')->badge(),
                                Infolists\Components\TextEntry::make('guardian_name')->label('Parent / Guardian'),
                                Infolists\Components\TextEntry::make('guardian_phone')->label('Guardian Contact'),
                                Infolists\Components\TextEntry::make('address')->columnSpanFull(),
                            ])->columns(3),
                    ])->columnSpanFull(),
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
