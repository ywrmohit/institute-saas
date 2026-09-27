<?php

namespace App\Filament\Franchise\Resources;

use App\Filament\Franchise\Resources\ExamResource\Pages;
use App\Models\Batch;
use App\Models\Exam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationGroup = 'Examinations & Certs';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('view_exams'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('manage_exams'));
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('manage_exams'));
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('manage_exams'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Exam Setup')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Exam Overview')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Exam Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g. Mid-Term MCQ Assessment on Web Development'),
                                Forms\Components\Select::make('course_id')
                                    ->relationship('course', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->reactive(),
                                Forms\Components\Select::make('batch_id')
                                    ->label('Specific Batch (Optional)')
                                    ->options(fn(Forms\Get $get) => Batch::where('course_id', $get('course_id'))->pluck('name', 'id'))
                                    ->searchable()
                                    ->nullable(),
                                Forms\Components\Select::make('exam_type')
                                    ->options([
                                        'mcq' => 'Online Multiple Choice (MCQ)',
                                        'practical' => 'Lab Practical & Viva',
                                        'theory' => 'Written Theory',
                                        'hybrid' => 'Comprehensive Hybrid',
                                    ])
                                    ->default('mcq')
                                    ->required(),
                                Forms\Components\TextInput::make('total_marks')
                                    ->numeric()
                                    ->default(100)
                                    ->required(),
                                Forms\Components\TextInput::make('passing_marks')
                                    ->numeric()
                                    ->default(40)
                                    ->required(),
                                Forms\Components\TextInput::make('duration_minutes')
                                    ->label('Duration (Minutes)')
                                    ->numeric()
                                    ->default(60)
                                    ->required(),
                                Forms\Components\DateTimePicker::make('exam_date')
                                    ->label('Scheduled Exam Date & Time'),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published & Live',
                                        'completed' => 'Completed / Evaluated',
                                    ])
                                    ->default('published')
                                    ->required(),
                                Forms\Components\Textarea::make('instructions')
                                    ->rows(3)
                                    ->placeholder('Instructions for candidates...')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('MCQ Question Bank')
                            ->visible(fn(Forms\Get $get) => in_array($get('exam_type'), ['mcq', 'hybrid']))
                            ->schema([
                                Forms\Components\Repeater::make('questions')
                                    ->relationship('questions')
                                    ->schema([
                                        Forms\Components\Textarea::make('question_text')
                                            ->label('Question')
                                            ->required()
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        Forms\Components\KeyValue::make('options')
                                            ->label('Answer Options (Key: A, B, C, D | Value: Option Text)')
                                            ->keyLabel('Option')
                                            ->valueLabel('Text')
                                            ->default([
                                                'A' => '',
                                                'B' => '',
                                                'C' => '',
                                                'D' => '',
                                            ])
                                            ->required(),
                                        Forms\Components\TextInput::make('correct_answer')
                                            ->label('Correct Key')
                                            ->placeholder('e.g. A')
                                            ->required(),
                                        Forms\Components\TextInput::make('marks')
                                            ->numeric()
                                            ->default(1)
                                            ->required(),
                                        Forms\Components\TextInput::make('explanation')
                                            ->placeholder('Optional explanation for student feedback')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->itemLabel(fn(array $state): ?string => $state['question_text'] ?? null),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('course.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exam_type')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn(string $state): string => strtoupper($state)),
                Tables\Columns\TextColumn::make('total_marks')
                    ->label('Total / Pass')
                    ->formatStateUsing(fn(Exam $record): string => "{$record->total_marks} / {$record->passing_marks}"),
                Tables\Columns\TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('MCQs')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('results_count')
                    ->counts('results')
                    ->label('Submissions')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'published' => 'success',
                        'completed' => 'primary',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->relationship('course', 'name'),
                Tables\Filters\SelectFilter::make('exam_type')
                    ->options([
                        'mcq' => 'MCQ',
                        'practical' => 'Practical',
                        'theory' => 'Theory',
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
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
        ];
    }
}
