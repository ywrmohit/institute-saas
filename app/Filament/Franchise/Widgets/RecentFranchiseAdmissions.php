<?php

namespace App\Filament\Franchise\Widgets;

use App\Models\Student;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentFranchiseAdmissions extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::query()->latest()->limit(5)
            )
            ->heading('Recent Student Admissions')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->circular(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Student Name')
                    ->weight('bold')
                    ->description(fn(Student $record): string => "ID: {$record->student_id_code}"),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('phone')
                    ->icon('heroicon-m-phone'),
                Tables\Columns\TextColumn::make('admission_date')
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'primary',
                        default => 'gray',
                    }),
            ]);
    }
}
