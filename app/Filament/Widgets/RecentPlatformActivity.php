<?php

namespace App\Filament\Widgets;

use App\Models\AuditLog;
use App\Models\Student;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPlatformActivity extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AuditLog::query()->latest()->limit(10)
            )
            ->heading('Recent Platform Activity & Audit Stream')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('franchise.name')
                    ->label('Institute')
                    ->default('Central Platform')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->default('System')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn(string $state): string => match(true) {
                        str_contains($state, 'delete') => 'danger',
                        str_contains($state, 'payment') => 'success',
                        str_contains($state, 'create') || str_contains($state, 'cert') => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP'),
            ]);
    }
}
