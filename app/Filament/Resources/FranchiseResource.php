<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FranchiseResource\Pages;
use App\Models\Franchise;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class FranchiseResource extends Resource
{
    protected static ?string $model = Franchise::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'SaaS Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Franchise Profile')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General Information')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Institute / Franchise Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation === 'create') {
                                            $set('slug', Str::slug($state));
                                            $set('code', strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $state), 0, 4)) . rand(100, 999));
                                        }
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Used for URL subdomain/path: /app/{slug}'),
                                Forms\Components\TextInput::make('code')
                                    ->label('Franchise Code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),
                                Forms\Components\FileUpload::make('logo')
                                    ->image()
                                    ->directory('franchise-logos')
                                    ->avatar(),
                                Forms\Components\TextInput::make('tax_number')
                                    ->label('GST / Tax ID Number')
                                    ->maxLength(50),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'suspended' => 'Suspended',
                                        'pending' => 'Pending Review',
                                    ])
                                    ->default('active')
                                    ->required(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Contact & Location')
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('city')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('state')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('country')
                                    ->default('India')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('address')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Subscription & Limits')
                            ->schema([
                                Forms\Components\Select::make('subscription_plan_id')
                                    ->label('Subscription Plan')
                                    ->relationship('subscriptionPlan', 'name')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $plan = SubscriptionPlan::find($state);
                                        if ($plan) {
                                            $set('max_branches', $plan->max_branches);
                                            $set('max_students', $plan->max_students);
                                        }
                                    }),
                                Forms\Components\DateTimePicker::make('plan_expires_at')
                                    ->label('Plan Expiry Date')
                                    ->default(now()->addYear()),
                                Forms\Components\TextInput::make('max_branches')
                                    ->label('Branch Limit')
                                    ->numeric()
                                    ->default(3)
                                    ->required(),
                                Forms\Components\TextInput::make('max_students')
                                    ->label('Student Capacity Limit')
                                    ->numeric()
                                    ->default(200)
                                    ->required(),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(Franchise $record): string => "Code: {$record->code}"),
                Tables\Columns\TextColumn::make('subscriptionPlan.name')
                    ->label('Plan')
                    ->badge()
                    ->color('info')
                    ->default('Free Trial'),
                Tables\Columns\TextColumn::make('branches_count')
                    ->counts('branches')
                    ->label('Branches')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('students_count')
                    ->counts('students')
                    ->label('Students')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('plan_expires_at')
                    ->label('Expires')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                    ]),
                Tables\Filters\SelectFilter::make('subscription_plan_id')
                    ->relationship('subscriptionPlan', 'name')
                    ->label('Plan'),
            ])
            ->actions([
                Tables\Actions\Action::make('openPortal')
                    ->label('Open Portal')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn(Franchise $record): string => url("/app/{$record->slug}"))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListFranchises::route('/'),
            'create' => Pages\CreateFranchise::route('/create'),
            'edit' => Pages\EditFranchise::route('/{record}/edit'),
        ];
    }
}
