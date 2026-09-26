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
}
