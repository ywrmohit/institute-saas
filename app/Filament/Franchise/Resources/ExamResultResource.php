<?php

namespace App\Filament\Franchise\Resources;

use App\Filament\Franchise\Resources\ExamResultResource\Pages;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExamResultResource extends Resource
{
    protected static ?string $model = ExamResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Examinations & Certs';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Exam Evaluation & Grading')
                    ->schema([
                        Forms\Components\Select::make('exam_id')
                            ->relationship('exam', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $exam = Exam::find($state);
                                if ($exam) {
                                    $set('total_marks', $exam->total_marks);
                                }
                            }),
                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->options(fn() => Student::all()->pluck('full_name', 'id'))
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('total_marks')
                            ->numeric()
                            ->required()
                            ->default(100),
                        Forms\Components\TextInput::make('marks_obtained')
                            ->label('Marks Obtained')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $obt = (float) $get('marks_obtained');
                                $tot = (float) $get('total_marks') ?: 100;
                                $pct = round(($obt / $tot) * 100, 2);
                                $set('percentage', $pct);
                                $set('grade', ExamResult::calculateGrade($pct));
                                $set('status', $pct >= 40 ? 'pass' : 'fail');
                            }),
                        Forms\Components\TextInput::make('percentage')
                            ->numeric()
                            ->suffix('%')
                            ->readOnly(),
                        Forms\Components\TextInput::make('grade')
                            ->readOnly(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pass' => 'Passed',
                                'fail' => 'Failed',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('feedback')
                            ->placeholder('Trainer evaluation feedback or notes...')
                            ->rows(3)
                            ->columnSpanFull(),
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
                    ->description(fn(ExamResult $record): string => "Roll: {$record->student?->student_id_code}"),
                Tables\Columns\TextColumn::make('exam.title')
                    ->label('Exam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('marks_obtained')
                    ->label('Score')
                    ->formatStateUsing(fn(ExamResult $record): string => "{$record->marks_obtained} / {$record->total_marks}"),
                Tables\Columns\TextColumn::make('percentage')
                    ->label('Percentage')
                    ->formatStateUsing(fn(string $state): string => "{$state}%")
                    ->sortable(),
                Tables\Columns\TextColumn::make('grade')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'A+', 'A' => 'success',
                        'B+', 'B' => 'info',
                        'C', 'D' => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'pass' ? 'success' : 'danger')
                    ->formatStateUsing(fn(string $state): string => strtoupper($state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Evaluation Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('exam_id')
                    ->relationship('exam', 'title')
                    ->label('Exam'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pass' => 'Passed',
                        'fail' => 'Failed',
                    ]),
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
            'index' => Pages\ListExamResults::route('/'),
            'create' => Pages\CreateExamResult::route('/create'),
            'edit' => Pages\EditExamResult::route('/{record}/edit'),
        ];
    }
}
