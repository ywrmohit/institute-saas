<?php

namespace App\Filament\Franchise\Resources;

use App\Filament\Franchise\Resources\TrainerResource\Pages;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class TrainerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Staff & Trainer';
    protected static ?string $pluralModelLabel = 'Staff & Trainers';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Center Management';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('view_trainers'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('manage_trainers'));
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('manage_trainers'));
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('manage_trainers'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->whereNotIn('role', ['super_admin', 'franchise_owner', 'student'])
            ->where('franchise_id', Filament::getTenant()?->id);

        if (auth()->user()?->isBranchAdmin() && auth()->user()->branch_id) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Staff / Faculty Member Profile')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
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
                            ->label('Assigned Role')
                            ->options(function () {
                                $roles = \Spatie\Permission\Models\Role::whereNotIn('name', ['super_admin', 'franchise_owner', 'student'])->get();
                                if ($roles->isEmpty()) {
                                    return [
                                        'trainer' => 'Trainer / Faculty',
                                        'branch_admin' => 'Branch Center Admin',
                                        'accountant' => 'Accountant / Cashier',
                                    ];
                                }
                                return $roles->pluck('name', 'name')
                                    ->mapWithKeys(fn($r) => [$r => ucwords(str_replace('_', ' ', $r))])
                                    ->toArray();
                            })
                            ->default('trainer')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('branch_id')
                            ->relationship('branch', 'name')
                            ->label('Branch Assignment')
                            ->nullable()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('designation')
                            ->placeholder('e.g. Senior Python & Java Trainer')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
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
                    ->description(fn(User $record): string => $record->designation ?? ''),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'branch_admin' => 'primary',
                        'trainer' => 'info',
                        'accountant' => 'success',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Assigned Branch')
                    ->default('All Branches')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'active' ? 'success' : 'gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options(function () {
                        return \Spatie\Permission\Models\Role::whereNotIn('name', ['super_admin', 'franchise_owner', 'student'])
                            ->pluck('name', 'name')
                            ->mapWithKeys(fn($r) => [$r => ucwords(str_replace('_', ' ', $r))])
                            ->toArray();
                    }),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->relationship('branch', 'name')
                    ->label('Branch'),
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
            'index' => Pages\ListTrainers::route('/'),
            'create' => Pages\CreateTrainer::route('/create'),
            'edit' => Pages\EditTrainer::route('/{record}/edit'),
        ];
    }
}
