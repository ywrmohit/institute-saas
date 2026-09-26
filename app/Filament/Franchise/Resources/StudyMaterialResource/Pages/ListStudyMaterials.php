<?php

namespace App\Filament\Franchise\Resources\StudyMaterialResource\Pages;

use App\Filament\Franchise\Resources\StudyMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudyMaterials extends ListRecords
{
    protected static string $resource = StudyMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
