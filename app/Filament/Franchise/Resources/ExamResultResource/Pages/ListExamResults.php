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

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Resources\Pages\ListRecords\Tab::make('All Evaluations')
                ->badge(\App\Models\ExamResult::count()),
            'pass' => \Filament\Resources\Pages\ListRecords\Tab::make('Passed')
                ->badge(\App\Models\ExamResult::where('status', 'pass')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'pass')),
            'fail' => \Filament\Resources\Pages\ListRecords\Tab::make('Failed / Retest')
                ->badge(\App\Models\ExamResult::where('status', 'fail')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'fail')),
        ];
    }
}
