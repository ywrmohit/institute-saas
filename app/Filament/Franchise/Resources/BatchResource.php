<?php

namespace App\Filament\Franchise\Resources;

use App\Filament\Franchise\Resources\BatchResource\Pages;
use App\Models\Batch;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BatchResource extends Resource
{
    protected static ?string $model = Batch::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Academics';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Batch Identification & Schedule')
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->relationship('branch', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('course_id')
                            ->relationship('course', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('trainer_id')
                            ->label('Assigned Trainer / Faculty')
                            ->options(fn() => User::where('role', 'trainer')->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),
                        Forms\Components\TextInput::make('name')
                            ->label('Batch Name / Title')
                            ->required()
                            ->placeholder('e.g. DCA Morning Batch A'),
                        Forms\Components\TextInput::make('batch_code')
                            ->required()
                            ->placeholder('e.g. DCA-2024-M1'),
                        Forms\Components\TextInput::make('time_slot')
                            ->label('Timing Slot')
                            ->required()
                            ->placeholder('e.g. 09:00 AM - 11:00 AM (Mon - Fri)'),
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->default(today()),
                        Forms\Components\DatePicker::make('end_date')
                            ->nullable(),
                        Forms\Components\TextInput::make('capacity')
                            ->label('Student Capacity')
                            ->numeric()
                            ->default(30)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'upcoming' => 'Upcoming',
                                'active' => 'Active / Ongoing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(Batch $record): string => "Code: {$record->batch_code}"),
                Tables\Columns\TextColumn::make('course.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('trainer.name')
                    ->label('Trainer')
                    ->default('Unassigned')
                    ->searchable(),
                Tables\Columns\TextColumn::make('time_slot')
                    ->label('Timings')
                    ->icon('heroicon-m-clock'),
                Tables\Columns\TextColumn::make('enrollments_count')
                    ->counts('enrollments')
                    ->label('Enrolled')
                    ->badge()
                    ->color(fn(Batch $record): string => ($record->enrollments_count >= $record->capacity) ? 'danger' : 'success')
                    ->formatStateUsing(fn(string $state, Batch $record): string => "{$state} / {$record->capacity}"),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'upcoming' => 'info',
                        'completed' => 'gray',
                        default => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->relationship('branch', 'name')
                    ->label('Branch'),
                Tables\Filters\SelectFilter::make('course_id')
                    ->relationship('course', 'name')
                    ->label('Course'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'upcoming' => 'Upcoming',
                        'active' => 'Active',
                        'completed' => 'Completed',
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
            'index' => Pages\ListBatches::route('/'),
            'create' => Pages\CreateBatch::route('/create'),
            'edit' => Pages\EditBatch::route('/{record}/edit'),
        ];
    }
}
