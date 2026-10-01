<?php

namespace App\Filament\Franchise\Pages;

use App\Models\Certificate;
use App\Models\Franchise;
use App\Models\Payment;
use App\Models\Student;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class InstituteProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Settings & Administration';
    protected static ?string $navigationLabel = 'Institute Profile';
    protected static ?string $title = 'Institute Profile & Organization Branding';
    protected static ?string $slug = 'institute-profile';
    protected static ?int $navigationSort = 97;

    protected static string $view = 'filament.franchise.pages.institute-profile';

    public ?array $data = [];
    public ?Franchise $franchise = null;

    public int $totalStudents = 0;
    public int $totalBranches = 0;
    public int $totalCourses = 0;
    public int $totalBatches = 0;
    public int $totalCertificates = 0;

    public $sampleStudent = null;
    public $sampleCertificate = null;
    public $samplePayment = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isFranchiseOwner() || $user->isBranchAdmin() || $user->franchise_id !== null);
    }

    public static function getNavigationUrl(): string
    {
        $tenant = Filament::getTenant() ?? auth()->user()?->franchise;
        return $tenant ? static::getUrl(['tenant' => $tenant]) : '#';
    }

    public function mount(): void
    {
        $tenant = Filament::getTenant();
        if (!$tenant && auth()->check() && auth()->user()->franchise_id) {
            $tenant = Franchise::find(auth()->user()->franchise_id);
        }

        if (!$tenant) {
            abort(404, 'Institute / Franchise context not found.');
        }

        $this->franchise = $tenant;

        // Calculate live metrics
        $this->totalStudents = $this->franchise->students()->count();
        $this->totalBranches = $this->franchise->branches()->count();
        $this->totalCourses = $this->franchise->courses()->count();
        $this->totalBatches = $this->franchise->batches()->count();
        $this->totalCertificates = $this->franchise->certificates()->count();

        // Load sample records for live document preview studio
        $this->sampleStudent = $this->franchise->students()->with(['branch', 'enrollments.course', 'enrollments.batch'])->first();
        $this->sampleCertificate = $this->franchise->certificates()->with(['student', 'course', 'branch'])->first();
        $this->samplePayment = Payment::where('franchise_id', $this->franchise->id)->with(['student', 'feeInvoice.installments', 'branch', 'receivedBy'])->first();

        $this->form->fill([
            'name' => $this->franchise->name,
            'code' => $this->franchise->code,
            'tagline' => $this->franchise->tagline,
            'email' => $this->franchise->email,
            'phone' => $this->franchise->phone,
            'website' => $this->franchise->website,
            'logo' => $this->franchise->logo,
            'stamp' => $this->franchise->stamp,
            'signature' => $this->franchise->signature,
            'address' => $this->franchise->address,
            'city' => $this->franchise->city,
            'state' => $this->franchise->state,
            'country' => $this->franchise->country ?? 'India',
            'tax_number' => $this->franchise->tax_number,
        ]);
    }

    public function form(Form $form): Form
    {
        $canEdit = auth()->user()->isSuperAdmin() || auth()->user()->isFranchiseOwner() || auth()->user()->isBranchAdmin();

        return $form
            ->schema([
                Forms\Components\Section::make('Official Institutional Branding & Seals')
                    ->description('Upload your institute logo, official stamp seal, and director signature. These are rendered onto student PVC ID cards, completion certificates, and fee receipts.')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Institute Logo')
                            ->image()
                            ->directory('franchises/logos')
                            ->disk('public')
                            ->maxSize(2048)
                            ->imageResizeMode('contain')
                            ->imageCropAspectRatio('1:1')
                            ->helperText('Displayed on the public portal header, invoices, and certificates.')
                            ->disabled(!$canEdit),

                        Forms\Components\FileUpload::make('stamp')
                            ->label('Official Stamp / Seal')
                            ->image()
                            ->directory('franchises/stamps')
                            ->disk('public')
                            ->maxSize(2048)
                            ->helperText('Official circular institute seal imprinted on authentic graduation certificates.')
                            ->disabled(!$canEdit),

                        Forms\Components\FileUpload::make('signature')
                            ->label('Authorized Signatory Signature')
                            ->image()
                            ->directory('franchises/signatures')
                            ->disk('public')
                            ->maxSize(2048)
                            ->helperText('Transparent PNG signature of the Academic Director / Center Head.')
                            ->disabled(!$canEdit),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('General Institute Identification')
                    ->description('Primary legal name, unique accreditation code, and official motto.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Institute Legal Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Apex Institute of Information Technology')
                            ->disabled(!$canEdit),

                        Forms\Components\TextInput::make('code')
                            ->label('Unique Institute Code')
                            ->disabled()
                            ->helperText('Assigned by Super Admin. Used in student IDs and admission numbers.'),

                        Forms\Components\TextInput::make('tagline')
                            ->label('Tagline / Slogan')
                            ->maxLength(255)
                            ->placeholder('e.g. Empowering Next-Gen Technical Leaders')
                            ->disabled(!$canEdit),

                        Forms\Components\TextInput::make('tax_number')
                            ->label('Tax ID / GSTIN')
                            ->maxLength(50)
                            ->placeholder('e.g. 27AAACA1234B1Z5')
                            ->helperText('Printed on tax-compliant fee invoices and billing receipts.')
                            ->disabled(!$canEdit),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Official Contact & Digital Presence')
                    ->description('Primary helpline, inquiry email, and web address for prospective students and parents.')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Official Support / Inquiry Email')
                            ->email()
                            ->required()
                            ->maxLength(150)
                            ->disabled(!$canEdit),

                        Forms\Components\TextInput::make('phone')
                            ->label('Primary Helpline / Phone')
                            ->tel()
                            ->required()
                            ->maxLength(50)
                            ->disabled(!$canEdit),

                        Forms\Components\TextInput::make('website')
                            ->label('Official Website URL')
                            ->url()
                            ->placeholder('https://yourinstitute.edu.in')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->disabled(!$canEdit),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Registered Headquarters Address')
                    ->description('Physical campus / central office address for legal correspondence and billing.')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Street Address')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->disabled(!$canEdit),

                        Forms\Components\TextInput::make('city')
                            ->label('City')
                            ->maxLength(100)
                            ->disabled(!$canEdit),

                        Forms\Components\TextInput::make('state')
                            ->label('State / Province')
                            ->maxLength(100)
                            ->disabled(!$canEdit),

                        Forms\Components\TextInput::make('country')
                            ->label('Country')
                            ->default('India')
                            ->maxLength(100)
                            ->disabled(!$canEdit),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->isFranchiseOwner() && !$user->isBranchAdmin()) {
            Notification::make()
                ->title('Access Restricted')
                ->body('Only Franchise Owners and Branch Administrators have permission to modify institute profile details.')
                ->danger()
                ->send();
            return;
        }

        $validated = $this->form->getState();

        // Do not overwrite code
        unset($validated['code']);

        $this->franchise->update($validated);
        $this->franchise->refresh();

        // Refresh sample records for instant live preview synchronization
        $this->sampleStudent = $this->franchise->students()->with(['branch', 'enrollments.course', 'enrollments.batch'])->first();
        $this->sampleCertificate = $this->franchise->certificates()->with(['student', 'course', 'branch'])->first();
        $this->samplePayment = Payment::where('franchise_id', $this->franchise->id)->with(['student', 'feeInvoice.installments', 'branch', 'receivedBy'])->first();

        Notification::make()
            ->title('Institute Profile Updated')
            ->body('Institutional branding, contact details, and organization credentials updated successfully.')
            ->success()
            ->send();
    }
}
