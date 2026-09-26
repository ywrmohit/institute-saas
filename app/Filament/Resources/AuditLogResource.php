<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Access & Security';
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('action')
                    ->disabled(),
                Forms\Components\TextInput::make('ip_address')
                    ->disabled(),
                Forms\Components\Textarea::make('user_agent')
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('old_values')
                    ->disabled(),
                Forms\Components\KeyValue::make('new_values')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Actor')
                    ->default('System / Guest')
                    ->searchable(),
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn(string $state): string => match(true) {
                        str_contains($state, 'delete') => 'danger',
                        str_contains($state, 'create') || str_contains($state, 'issue') => 'success',
                        str_contains($state, 'payment') => 'warning',
                        default => 'info',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('franchise.name')
                    ->label('Franchise')
                    ->default('Central')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model_type')
                    ->label('Entity')
                    ->formatStateUsing(fn(?string $state): string => $state ? class_basename($state) : '-'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('franchise_id')
                    ->relationship('franchise', 'name')
                    ->label('Franchise'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}
