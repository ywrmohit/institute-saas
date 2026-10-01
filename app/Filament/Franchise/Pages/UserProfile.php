<?php

namespace App\Filament\Franchise\Pages;

use App\Models\Franchise;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Settings & Administration';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $title = 'Personal Profile & Account Settings';
    protected static ?string $slug = 'profile';
    protected static ?int $navigationSort = 96;

    protected static string $view = 'filament.franchise.pages.user-profile';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public static function getNavigationUrl(): string
    {
        $tenant = Filament::getTenant() ?? auth()->user()?->franchise;
        return $tenant ? static::getUrl(['tenant' => $tenant]) : '#';
    }

    public function mount(): void
    {
        $user = auth()->user();

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'designation' => $user->designation,
            'avatar' => $user->avatar,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(auth()->user())
            ->schema([
                Forms\Components\Section::make('Profile Information')
                    ->description('Manage your personal details, designation, and official contact information.')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Profile Picture / Avatar')
                            ->image()
                            ->avatar()
                            ->directory('avatars')
                            ->disk('public')
                            ->maxSize(2048)
                            ->columnSpanFull()
                            ->helperText('Your photo will appear in the top navigation header and staff directory.'),

                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignorable: auth()->user()),

                        Forms\Components\TextInput::make('phone')
                            ->label('Mobile Phone')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('e.g. +91 98765 43210'),

                        Forms\Components\TextInput::make('designation')
                            ->label('Professional Designation')
                            ->placeholder('e.g. Center Head, Senior Trainer, Accounts Officer')
                            ->maxLength(100),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Security & Password')
                    ->description('Update your account login password. Leave blank if you wish to retain your current password.')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->revealable()
                            ->nullable()
                            ->requiredWith('new_password')
                            ->currentPassword(),

                        Forms\Components\TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->nullable()
                            ->rule(Password::default())
                            ->same('new_password_confirmation'),

                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->revealable()
                            ->nullable()
                            ->requiredWith('new_password'),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $user = auth()->user();
        $validated = $this->form->getState();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'designation' => $validated['designation'] ?? null,
        ];

        if (array_key_exists('avatar', $validated)) {
            $avatar = is_array($validated['avatar']) ? (reset($validated['avatar']) ?: null) : $validated['avatar'];
            $updateData['avatar'] = $avatar;
        }

        if (!empty($validated['new_password'])) {
            $updateData['password'] = Hash::make($validated['new_password']);
        }

        $user->update($updateData);

        // Refresh authentication instance so header avatar and navigation update immediately
        auth()->setUser($user->fresh());

        // Refresh form with latest values
        $freshUser = auth()->user();
        $this->form->fill([
            'name' => $freshUser->name,
            'email' => $freshUser->email,
            'phone' => $freshUser->phone,
            'designation' => $freshUser->designation,
            'avatar' => $freshUser->avatar,
        ]);

        Notification::make()
            ->title('Profile Saved')
            ->body('Your personal details and profile picture have been updated successfully.')
            ->success()
            ->send();
    }
}
