<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class EditProfile extends BaseEditProfile
{
    protected static string $view = 'filament.pages.auth.edit-profile';

    public function getMaxWidth(): MaxWidth | string | null
    {
        return MaxWidth::FourExtraLarge;
    }

    public function getTitle(): string | Htmlable
    {
        return 'Personal Profile & Account Settings';
    }

    public function getHeading(): string | Htmlable
    {
        return 'Personal Profile & Account Settings';
    }

    public function getSubheading(): ?string
    {
        return 'Manage your personal details, designation, and security credentials.';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Profile Information')
                    ->description('Manage your personal details, designation, and contact information.')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Profile Picture / Avatar')
                            ->image()
                            ->avatar()
                            ->directory('avatars')
                            ->disk('public')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        TextInput::make('phone')
                            ->label('Mobile Phone')
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('designation')
                            ->label('Professional Designation')
                            ->placeholder('e.g. Center Head, Senior Trainer, Accounts Officer')
                            ->maxLength(100),
                    ])->columns(2),

                Section::make('Security & Password')
                    ->description('Leave password fields blank if you do not wish to change your password.')
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])->columns(2),
            ])
            ->operation('edit')
            ->model($this->getUser())
            ->statePath('data');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('avatar', $data) && is_array($data['avatar'])) {
            $data['avatar'] = reset($data['avatar']) ?: null;
        }

        return parent::mutateFormDataBeforeSave($data);
    }

    protected function afterSave(): void
    {
        if (auth()->check()) {
            auth()->setUser(auth()->user()->fresh());
        }
    }
}
