<?php

namespace App\Filament\Franchise\Resources\CourseResource\Pages;

use App\Filament\Franchise\Resources\CourseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Resources\Pages\ListRecords\Tab::make('All Courses')
                ->badge(\App\Models\Course::count()),
            'active' => \Filament\Resources\Pages\ListRecords\Tab::make('Active Courses')
                ->badge(\App\Models\Course::where('status', 'active')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'active')),
            'archived' => \Filament\Resources\Pages\ListRecords\Tab::make('Archived')
                ->badge(\App\Models\Course::where('status', 'archived')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'archived')),
        ];
    }
}
