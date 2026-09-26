<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionPlanResource\Pages;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'SaaS Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plan Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Standard Institute Growth'),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('e.g. standard-growth'),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('₹')
                            ->default(2999.00),
                        Forms\Components\Select::make('billing_interval')
                            ->options([
                                'monthly' => 'Monthly',
                                'yearly' => 'Yearly',
                                'quarterly' => 'Quarterly',
                            ])
                            ->default('monthly')
                            ->required(),
                        Forms\Components\TextInput::make('max_branches')
                            ->label('Max Branches Allowed')
                            ->numeric()
                            ->default(3)
                            ->required(),
                        Forms\Components\TextInput::make('max_students')
                            ->label('Max Active Students')
                            ->numeric()
                            ->default(300)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Plan Active for Subscriptions')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Plan Features')
                    ->schema([
                        Forms\Components\TagsInput::make('features')
                            ->placeholder('Add feature and press Enter (e.g. Multi-branch, Online MCQ Exams, QR Certificates)')
                            ->helperText('List features bundled in this SaaS tier.'),
                    ]),
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
                Tables\Columns\TextColumn::make('price')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('billing_interval')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('max_branches')
                    ->label('Branches')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_students')
                    ->label('Students')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('franchises_count')
                    ->counts('franchises')
                    ->label('Subscribed Institutes')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Plans'),
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
            'index' => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'edit' => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}
