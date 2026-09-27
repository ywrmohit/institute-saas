<?php

namespace App\Filament\Franchise\Resources\RoleResource\Pages;

use App\Filament\Franchise\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Custom Role')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
