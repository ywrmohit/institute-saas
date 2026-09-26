<?php

namespace App\Filament\Franchise\Resources\FeeInvoiceResource\Pages;

use App\Filament\Franchise\Resources\FeeInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeeInvoices extends ListRecords
{
    protected static string $resource = FeeInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
