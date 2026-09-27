<?php

namespace App\Filament\Franchise\Resources\BatchResource\Pages;

use App\Filament\Franchise\Resources\BatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBatches extends ListRecords
{
    protected static string $resource = BatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Resources\Pages\ListRecords\Tab::make('All Batches')
                ->badge(\App\Models\Batch::count()),
            'active' => \Filament\Resources\Pages\ListRecords\Tab::make('Running Batches')
                ->badge(\App\Models\Batch::where('status', 'active')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'active')),
            'upcoming' => \Filament\Resources\Pages\ListRecords\Tab::make('Upcoming')
                ->badge(\App\Models\Batch::where('status', 'upcoming')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'upcoming')),
            'completed' => \Filament\Resources\Pages\ListRecords\Tab::make('Completed')
                ->badge(\App\Models\Batch::where('status', 'completed')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'completed')),
        ];
    }
}
