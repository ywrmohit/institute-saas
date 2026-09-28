<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CustomizationSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Platform Customization';
    protected static ?string $title = 'Platform Branding & Customization';
    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.customization-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'app_name' => SystemSetting::get('app_name', null, 'Remax'),
            'app_tagline' => SystemSetting::get('app_tagline', null, 'Multi-Tenant Institute & Franchise Management SaaS'),
            'support_email' => SystemSetting::get('support_email', null, 'admin@remax.io'),
            'support_phone' => SystemSetting::get('support_phone', null, '+91 98765 43200'),
            'footer_text' => SystemSetting::get('footer_text', null, 'Remax SaaS Platform. All rights reserved.'),
            'hero_badge' => SystemSetting::get('hero_badge', null, 'Complete Multi-Tenant Architecture & Franchise Management'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Branding & Identity')
                    ->description('Configure primary platform name, slogan, and copyright text dynamically reflected across all portals.')
                    ->schema([
                        Forms\Components\TextInput::make('app_name')
                            ->label('Platform / Portal Name')
                            ->required()
                            ->maxLength(100)
                            ->helperText('Displayed in browser titles, portal headers, student app, and notifications.'),
                        Forms\Components\TextInput::make('app_tagline')
                            ->label('Tagline / Slogan')
                            ->maxLength(255)
                            ->helperText('Subtitle displayed on landing page and documentation.'),
                        Forms\Components\TextInput::make('footer_text')
                            ->label('Footer Copyright Notice')
                            ->maxLength(255)
                            ->helperText('Footer text displayed at the bottom of public and student views.'),
                        Forms\Components\TextInput::make('hero_badge')
                            ->label('Landing Page Hero Badge')
                            ->maxLength(255)
                            ->helperText('Highlighted pill badge shown above the main heading.'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact & Support Details')
                    ->description('Platform contact information shown to institutes, staff, and students.')
                    ->schema([
                        Forms\Components\TextInput::make('support_email')
                            ->label('Support Email Address')
                            ->email()
                            ->required()
                            ->maxLength(150),
                        Forms\Components\TextInput::make('support_phone')
                            ->label('Support Phone / Helpline')
                            ->tel()
                            ->maxLength(50),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            SystemSetting::set($key, $value);
        }

        Notification::make()
            ->title('Platform Customization Saved')
            ->body('Portal branding and customization settings updated successfully.')
            ->success()
            ->send();
    }
}
