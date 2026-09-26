<?php

namespace App\Filament\Franchise\Resources\TrainerResource\Pages;

use App\Filament\Franchise\Resources\TrainerResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainer extends CreateRecord
{
    protected static string $resource = TrainerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['franchise_id'] = Filament::getTenant()?->id;
        return $data;
    }
}
