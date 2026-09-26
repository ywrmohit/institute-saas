<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Access & Security';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Account')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Select::make('role')
                            ->options([
                                'super_admin' => 'Super Admin (Platform Owner)',
                                'franchise_owner' => 'Franchise Owner',
                                'branch_admin' => 'Branch Admin',
                                'trainer' => 'Trainer / Faculty',
                                'accountant' => 'Accountant / Cashier',
                                'student' => 'Student',
                            ])
                            ->default('franchise_owner')
                            ->required()
                            ->reactive(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Tenant & Branch Assignment')
                    ->schema([
                        Forms\Components\Select::make('franchise_id')
                            ->label('Franchise / Institute')
                            ->relationship('franchise', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get): bool => $get('role') !== 'super_admin')
                            ->reactive(),
                        Forms\Components\Select::make('branch_id')
                            ->label('Branch Location')
                            ->relationship('branch', 'name', fn ($query, Forms\Get $get) => $query->where('franchise_id', $get('franchise_id')))
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get): bool => in_array($get('role'), ['branch_admin', 'trainer', 'accountant', 'student'])),
                        Forms\Components\TextInput::make('designation')
                            ->label('Job Designation / Title')
                            ->placeholder('e.g. Senior Faculty, Center Head')
                            ->maxLength(255),
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
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'franchise_owner' => 'warning',
                        'branch_admin' => 'primary',
                        'trainer' => 'info',
                        'accountant' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('franchise.name')
                    ->label('Franchise')
                    ->default('Platform Wide')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch')
                    ->default('All Branches'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'franchise_owner' => 'Franchise Owner',
                        'branch_admin' => 'Branch Admin',
                        'trainer' => 'Trainer',
                        'accountant' => 'Accountant',
                        'student' => 'Student',
                    ]),
                Tables\Filters\SelectFilter::make('franchise_id')
                    ->relationship('franchise', 'name')
                    ->label('Franchise'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
