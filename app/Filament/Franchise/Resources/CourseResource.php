<?php

namespace App\Filament\Franchise\Resources;

use App\Filament\Franchise\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Academics';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Course Configuration')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Course Details')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Course Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g. Diploma in Computer Applications (DCA)'),
                                Forms\Components\TextInput::make('code')
                                    ->label('Course Code')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('e.g. DCA-101'),
                                Forms\Components\Select::make('type')
                                    ->label('Course Type / Duration Category')
                                    ->options([
                                        'short_term' => 'Short-Term Certificate (1-2 Months)',
                                        '3_months' => '3-Month Certification',
                                        '6_months_diploma' => '6-Month Diploma',
                                        '1_year_diploma' => '1-Year Advanced Diploma',
                                        'advanced_diploma' => 'Master Diploma / Professional',
                                    ])
                                    ->default('6_months_diploma')
                                    ->required(),
                                Forms\Components\TextInput::make('category')
                                    ->placeholder('e.g. Programming, Graphic Design, Accounting, Hardware'),
                                Forms\Components\TextInput::make('duration_weeks')
                                    ->label('Duration (Weeks)')
                                    ->numeric()
                                    ->default(24)
                                    ->required(),
                                Forms\Components\TextInput::make('eligibility')
                                    ->placeholder('e.g. 10th Pass, 12th Pass, Graduate')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('certificate_title')
                                    ->label('Certificate Title')
                                    ->placeholder('e.g. Certified Full Stack Web Developer')
                                    ->maxLength(255),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'archived' => 'Archived',
                                    ])
                                    ->default('active')
                                    ->required(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Fee Structure')
                            ->schema([
                                Forms\Components\TextInput::make('registration_fee')
                                    ->label('Registration / Admission Fee')
                                    ->numeric()
                                    ->prefix('₹')
                                    ->default(500.00),
                                Forms\Components\TextInput::make('total_fee')
                                    ->label('Total Course Fee')
                                    ->numeric()
                                    ->prefix('₹')
                                    ->required()
                                    ->default(12000.00),
                                Forms\Components\Textarea::make('description')
                                    ->label('Course Description & Outcomes')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Curriculum & Modules')
                            ->schema([
                                Forms\Components\Repeater::make('modules')
                                    ->relationship('modules')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Module Title')
                                            ->required()
                                            ->placeholder('e.g. Module 1: Computer Fundamentals & MS Office'),
                                        Forms\Components\TextInput::make('duration_hours')
                                            ->label('Hours')
                                            ->numeric()
                                            ->default(15),
                                        Forms\Components\TextInput::make('order')
                                            ->label('Sequence #')
                                            ->numeric()
                                            ->default(1),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Topics Covered')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(1)
                                    ->collapsible()
                                    ->itemLabel(fn(array $state): ?string => $state['title'] ?? null),
                            ]),
                    ])->columnSpanFull(),
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
                    ->description(fn(Course $record): string => "Code: {$record->code}"),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'short_term' => 'Short-Term',
                        '3_months' => '3 Months',
                        '6_months_diploma' => '6 Mo Diploma',
                        '1_year_diploma' => '1 Yr Diploma',
                        default => 'Adv Diploma',
                    }),
                Tables\Columns\TextColumn::make('duration_weeks')
                    ->label('Weeks')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_fee')
                    ->label('Course Fee')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('batches_count')
                    ->counts('batches')
                    ->label('Active Batches')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'active' ? 'success' : 'gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'short_term' => 'Short-Term',
                        '3_months' => '3 Months',
                        '6_months_diploma' => '6 Months Diploma',
                        '1_year_diploma' => '1 Year Diploma',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'archived' => 'Archived',
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
