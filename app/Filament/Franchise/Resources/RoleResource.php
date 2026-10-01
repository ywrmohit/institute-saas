<?php

namespace App\Filament\Franchise\Resources;

use App\Filament\Franchise\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Settings & Administration';
    protected static ?string $navigationLabel = 'Roles & Permissions';
    protected static ?int $navigationSort = 99;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->can('manage_roles_permissions'));
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        // Protect foundational core system roles from accidental deletion
        if (in_array($record->name, ['super_admin', 'franchise_owner', 'branch_admin', 'trainer', 'accountant', 'student'])) {
            return false;
        }

        return static::canViewAny();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Role Identity')
                    ->description('Define role name and security scope')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Role Name (Identifier)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g. academic_counselor, lab_assistant, front_desk')
                            ->disabled(fn(?Role $record) => $record && in_array($record->name, ['super_admin', 'franchise_owner'])),
                        Forms\Components\Hidden::make('guard_name')
                            ->default('web'),
                    ])->columns(1),

                Forms\Components\Section::make('Granular Role Permissions')
                    ->description('Select the permissions allowed for this role across institute SaaS operations')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->columns(2)
                            ->gridDirection('row')
                            ->searchable()
                            ->bulkToggleable()
                            ->descriptions([
                                'view_students' => 'View student admission records & profile dossiers',
                                'create_students' => 'Enroll new students and complete 1-step admission flow',
                                'edit_students' => 'Update student bio, contact & qualification records',
                                'delete_students' => 'Remove or archive student records',
                                'transfer_students' => 'Transfer students across running classroom batches',
                                'print_student_id' => 'Generate and print official CR80 PVC Student ID cards',

                                'view_courses' => 'Access master course catalog and syllabus modules',
                                'manage_courses' => 'Author courses, pricing, duration and module curricula',
                                'view_batches' => 'View active and upcoming classroom batches',
                                'manage_batches' => 'Schedule batches, assign trainers, set capacities',
                                'view_attendance' => 'Inspect daily batch attendance registries and summaries',
                                'mark_attendance' => 'Mark present, absent, late, or leave for batch students',
                                'view_study_materials' => 'Access and download uploaded study notes & PDFs',
                                'manage_study_materials' => 'Upload and publish course documents & study materials',
                                'view_exams' => 'View online assessments and scheduled test exams',
                                'manage_exams' => 'Create online MCQ tests and question banks',
                                'evaluate_exams' => 'Score exams, award marks, and update letter grades',
                                'view_certificates' => 'Browse and verify student completion credentials',
                                'issue_certificates' => 'Issue official verifiable QR completion certificates',

                                'view_fee_invoices' => 'Inspect student fee invoices and balance ledgers',
                                'manage_fee_invoices' => 'Generate invoices, waive fees, or structure installments',
                                'view_payments' => 'View payment collection history and receipts',
                                'record_payments' => 'Collect cash, UPI, or card payments and issue receipts',
                                'print_receipts' => 'Print official printable payment receipts',

                                'view_branches' => 'Inspect franchise campus branches and facilities',
                                'manage_branches' => 'Add and configure campus physical branches',
                                'view_trainers' => 'View faculty trainers and administrative staff list',
                                'manage_trainers' => 'Invite, hire, and edit institute faculty and staff',
                                'view_announcements' => 'View institute broadcast notices and alerts',
                                'manage_announcements' => 'Post announcements to students and faculty',
                                'manage_roles_permissions' => 'Manage institute roles, permissions, and RBAC matrix',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Role Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->description(fn(Role $record): string => match ($record->name) {
                        'super_admin' => 'Global SaaS Platform Super Administrator',
                        'franchise_owner' => 'Institute Managing Director & Franchisee',
                        'branch_admin' => 'Campus Branch Center Head',
                        'trainer' => 'Instructional Faculty & Class Instructor',
                        'accountant' => 'Financial Billing & Collections Officer',
                        'student' => 'Enrolled Student Portal Member',
                        default => 'Custom Staff Role',
                    }),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions Granted')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Assigned Staff')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
