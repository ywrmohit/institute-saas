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

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Resources\Pages\ListRecords\Tab::make('All Materials')
                ->badge(\App\Models\StudyMaterial::count()),
            'published' => \Filament\Resources\Pages\ListRecords\Tab::make('Published')
                ->badge(\App\Models\StudyMaterial::where('is_published', true)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn($query) => $query->where('is_published', true)),
            'drafts' => \Filament\Resources\Pages\ListRecords\Tab::make('Drafts')
                ->badge(\App\Models\StudyMaterial::where('is_published', false)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn($query) => $query->where('is_published', false)),
        ];
    }
}
