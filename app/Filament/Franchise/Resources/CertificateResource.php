<?php

namespace App\Filament\Franchise\Resources;

use App\Filament\Franchise\Resources\CertificateResource\Pages;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Examinations & Certs';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('view_certificates'));
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('issue_certificates'));
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('issue_certificates'));
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('issue_certificates'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Certificate Generation')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->options(fn() => Student::all()->pluck('full_name', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $student = Student::with('enrollments.course')->find($state);
                                if ($student) {
                                    $set('branch_id', $student->branch_id);
                                    $enrollment = $student->enrollments()->latest()->first();
                                    if ($enrollment) {
                                        $set('course_id', $enrollment->course_id);
                                    }
                                }
                            }),
                        Forms\Components\Select::make('branch_id')
                            ->relationship('branch', 'name')
                            ->required(),
                        Forms\Components\Select::make('course_id')
                            ->relationship('course', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('certificate_number')
                            ->label('Certificate Number')
                            ->required()
                            ->default(fn() => 'CERT-' . date('Y') . '-' . strtoupper(Str::random(8))),
                        Forms\Components\TextInput::make('verification_code')
                            ->label('Security Verification UUID')
                            ->default(fn() => (string) Str::uuid())
                            ->readOnly()
                            ->required(),
                        Forms\Components\DatePicker::make('completion_date')
                            ->label('Course Completion Date')
                            ->default(today())
                            ->required(),
                        Forms\Components\DatePicker::make('issue_date')
                            ->label('Date of Issuance')
                            ->default(today())
                            ->required(),
                        Forms\Components\Select::make('grade')
                            ->options([
                                'Distinction' => 'Distinction (85%+)',
                                'First Class' => 'First Class (70% - 84%)',
                                'Second Class' => 'Second Class (55% - 69%)',
                                'Pass' => 'Pass (40% - 54%)',
                                'A+' => 'Grade A+',
                                'A' => 'Grade A',
                                'B+' => 'Grade B+',
                                'B' => 'Grade B',
                            ])
                            ->default('Distinction')
                            ->required(),
                        Forms\Components\TextInput::make('percentage')
                            ->numeric()
                            ->suffix('%')
                            ->placeholder('e.g. 88.5'),
                        Forms\Components\Select::make('template_name')
                            ->options([
                                'classic_blue' => 'Classic Corporate Blue (Default)',
                                'modern_gold' => 'Modern Gold & Navy',
                                'minimal_slate' => 'Minimalist Slate',
                            ])
                            ->default('classic_blue')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'issued' => 'Active & Issued',
                                'revoked' => 'Revoked',
                            ])
                            ->default('issued')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('certificate_number')
                    ->label('Cert #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grade')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('issue_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'issued' ? 'success' : 'danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->relationship('course', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'issued' => 'Issued',
                        'revoked' => 'Revoked',
                    ]),
                Tables\Filters\Filter::make('issue_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Issued After'),
                        Forms\Components\DatePicker::make('until')->label('Issued Before'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $d) => $q->whereDate('issue_date', '>=', $d))
                            ->when($data['until'], fn($q, $d) => $q->whereDate('issue_date', '<=', $d));
                    }),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\Action::make('print_certificate')
                    ->label('View / Print')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->url(fn(Certificate $record): string => route('certificate.print', ['code' => $record->verification_code]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('verify_link')
                    ->label('Verify Page')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->url(fn(Certificate $record): string => route('certificate.verify', ['code' => $record->verification_code]))
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
            'index' => Pages\ListCertificates::route('/'),
            'create' => Pages\CreateCertificate::route('/create'),
            'edit' => Pages\EditCertificate::route('/{record}/edit'),
        ];
    }
}
