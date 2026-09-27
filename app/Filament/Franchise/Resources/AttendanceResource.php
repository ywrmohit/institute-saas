<?php

namespace App\Filament\Franchise\Resources;

use App\Filament\Franchise\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\Batch;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('view_attendance'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('mark_attendance'));
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('mark_attendance'));
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('mark_attendance'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->isTrainer()) {
            $query->whereHas('batch', fn($q) => $q->where('trainer_id', $user->id));
        } elseif ($user?->isBranchAdmin() && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Attendance Record')
                    ->schema([
                        Forms\Components\Select::make('batch_id')
                            ->relationship('batch', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $batch = Batch::find($state);
                                if ($batch) {
                                    $set('branch_id', $batch->branch_id);
                                }
                            }),
                        Forms\Components\Hidden::make('branch_id'),
                        Forms\Components\Select::make('student_id')
                            ->label('Select Student')
                            ->options(function (Forms\Get $get) {
                                $batchId = $get('batch_id');
                                if (!$batchId) return Student::pluck('first_name', 'id');
                                return Student::whereHas('enrollments', fn($q) => $q->where('batch_id', $batchId))
                                    ->get()
                                    ->pluck('full_name', 'id');
                            })
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('date')
                            ->default(today())
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'present' => 'Present',
                                'absent' => 'Absent',
                                'late' => 'Late',
                                'leave' => 'Approved Leave',
                            ])
                            ->default('present')
                            ->required(),
                        Forms\Components\TextInput::make('remarks')
                            ->placeholder('Optional reason for late or leave')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Attendance $record): string => "Roll: {$record->student?->student_id_code}"),
                Tables\Columns\TextColumn::make('batch.name')
                    ->label('Batch')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'present' => 'success',
                        'late' => 'warning',
                        'leave' => 'info',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('remarks')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('batch_id')
                    ->relationship('batch', 'name')
                    ->label('Filter by Batch'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'leave' => 'Leave',
                    ]),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Date From'),
                        Forms\Components\DatePicker::make('until')->label('Date Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $d) => $q->whereDate('date', '>=', $d))
                            ->when($data['until'], fn($q, $d) => $q->whereDate('date', '<=', $d));
                    }),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->headerActions([
                Tables\Actions\Action::make('batch_mark')
                    ->label('Bulk Mark Batch Attendance')
                    ->icon('heroicon-o-check-badge')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('batch_id')
                            ->label('Select Batch')
                            ->relationship('batch', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('date')
                            ->label('Attendance Date')
                            ->default(today())
                            ->required(),
                        Forms\Components\Select::make('default_status')
                            ->label('Default Status for All Students')
                            ->options([
                                'present' => 'Mark All Present',
                                'absent' => 'Mark All Absent',
                            ])
                            ->default('present')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $batch = Batch::with('enrollments.student')->find($data['batch_id']);
                        if (!$batch) return;

                        $count = 0;
                        foreach ($batch->enrollments as $enrollment) {
                            if ($enrollment->student) {
                                Attendance::updateOrCreate(
                                    [
                                        'franchise_id' => $batch->franchise_id,
                                        'batch_id' => $batch->id,
                                        'student_id' => $enrollment->student_id,
                                        'date' => $data['date'],
                                    ],
                                    [
                                        'branch_id' => $batch->branch_id,
                                        'trainer_id' => auth()->id(),
                                        'status' => $data['default_status'],
                                    ]
                                );
                                $count++;
                            }
                        }

                        Notification::make()
                            ->title('Batch Attendance Recorded')
                            ->body("Recorded {$count} student attendances for {$batch->name}")
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
