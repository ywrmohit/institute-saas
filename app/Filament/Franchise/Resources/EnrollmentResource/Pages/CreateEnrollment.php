<?php

namespace App\Filament\Franchise\Resources\EnrollmentResource\Pages;

use App\Filament\Franchise\Resources\EnrollmentResource;
use App\Services\FeeService;
use Filament\Resources\Pages\CreateRecord;

class CreateEnrollment extends CreateRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function afterCreate(): void
    {
        $installments = (int) ($this->data['installments_count'] ?? 2);
        $feeService = app(FeeService::class);
        $feeService->createInvoiceForEnrollment($this->record, $installments);
    }
}
