<?php

namespace App\Filament\Franchise\Resources\ExamResultResource\Pages;

use App\Filament\Franchise\Resources\ExamResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExamResults extends ListRecords
{
    protected static string $resource = ExamResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
