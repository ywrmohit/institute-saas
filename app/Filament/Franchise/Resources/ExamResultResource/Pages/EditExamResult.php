<?php

namespace App\Filament\Franchise\Resources\ExamResultResource\Pages;

use App\Filament\Franchise\Resources\ExamResultResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamResult extends EditRecord
{
    protected static string $resource = ExamResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
