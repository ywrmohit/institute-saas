<?php

namespace App\Filament\Franchise\Resources;

use App\Filament\Franchise\Resources\EnrollmentResource\Pages;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Student Admissions';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->isBranchAdmin() && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($user?->isTrainer()) {
            $query->whereHas('batch', fn($q) => $q->where('trainer_id', $user->id));
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Admission & Batch Assignment')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Select Student')
                            ->options(fn() => Student::all()->pluck('full_name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('branch_id')
                            ->relationship('branch', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive(),
                        Forms\Components\Select::make('course_id')
                            ->relationship('course', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $course = Course::find($state);
                                if ($course) {
                                    $set('total_agreed_fee', $course->total_fee);
                                    $set('final_fee', $course->total_fee);
                                }
                            }),
                        Forms\Components\Select::make('batch_id')
                            ->label('Assign Batch')
                            ->options(function (Forms\Get $get) {
                                $courseId = $get('course_id');
                                $branchId = $get('branch_id');
                                $query = Batch::query();
                                if ($courseId) $query->where('course_id', $courseId);
                                if ($branchId) $query->where('branch_id', $branchId);
                                return $query->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('enrollment_date')
                            ->default(today())
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'enrolled' => 'Enrolled',
                                'completed' => 'Completed Course',
                                'dropped' => 'Dropped Out',
                            ])
                            ->default('enrolled')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Fee Agreement & Installment Setup')
                    ->schema([
                        Forms\Components\TextInput::make('total_agreed_fee')
                            ->label('Standard Course Fee')
                            ->numeric()
                            ->prefix('₹')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $fee = (float) $get('total_agreed_fee');
                                $disc = (float) $get('discount_amount');
                                $set('final_fee', max(0, $fee - $disc));
                            }),
                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Scholarship / Discount')
                            ->numeric()
                            ->prefix('₹')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $fee = (float) $get('total_agreed_fee');
                                $disc = (float) $get('discount_amount');
                                $set('final_fee', max(0, $fee - $disc));
                            }),
                        Forms\Components\TextInput::make('final_fee')
                            ->label('Final Payable Fee')
                            ->numeric()
                            ->prefix('₹')
                            ->readOnly()
                            ->required(),
                        Forms\Components\Select::make('installments_count')
                            ->label('Split Fee into Installments')
                            ->options([
                                1 => '1 (Full Payment Upfront)',
                                2 => '2 Installments (50% / 50%)',
                                3 => '3 Monthly Installments',
                                4 => '4 Monthly Installments',
                                6 => '6 Monthly Installments',
                            ])
                            ->default(2)
                            ->dehydrated(false)
                            ->helperText('An automated Fee Invoice & Installment schedule will be generated upon admission.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(Enrollment $record): string => "Roll: {$record->student?->student_id_code}"),
                Tables\Columns\TextColumn::make('course.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('batch.name')
                    ->label('Batch')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('final_fee')
                    ->label('Agreed Fee')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'enrolled' => 'success',
                        'completed' => 'primary',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('enrollment_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->relationship('course', 'name'),
                Tables\Filters\SelectFilter::make('batch_id')
                    ->relationship('batch', 'name'),
            ])
            ->actions([
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
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
