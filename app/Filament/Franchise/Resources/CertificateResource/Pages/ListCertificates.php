<?php

namespace App\Filament\Franchise\Resources\CertificateResource\Pages;

use App\Filament\Franchise\Resources\CertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCertificates extends ListRecords
{
    protected static string $resource = CertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Resources\Pages\ListRecords\Tab::make('All Certificates')
                ->badge(\App\Models\Certificate::count()),
            'issued' => \Filament\Resources\Pages\ListRecords\Tab::make('Active Issued')
                ->badge(\App\Models\Certificate::where('status', 'issued')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'issued')),
            'revoked' => \Filament\Resources\Pages\ListRecords\Tab::make('Revoked')
                ->badge(\App\Models\Certificate::where('status', 'revoked')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'revoked')),
        ];
    }
}
