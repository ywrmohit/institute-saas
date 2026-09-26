<?php

namespace App\Filament\Franchise\Resources\FeeInvoiceResource\Pages;

use App\Filament\Franchise\Resources\FeeInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeeInvoice extends EditRecord
{
    protected static string $resource = FeeInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
