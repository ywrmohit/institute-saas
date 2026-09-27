<?php

namespace App\Filament\Franchise\Resources\AttendanceResource\Pages;

use App\Filament\Franchise\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Resources\Pages\ListRecords\Tab::make('All Sessions')
                ->badge(\App\Models\Attendance::count()),
            'present' => \Filament\Resources\Pages\ListRecords\Tab::make('Present')
                ->badge(\App\Models\Attendance::where('status', 'present')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'present')),
            'absent' => \Filament\Resources\Pages\ListRecords\Tab::make('Absent')
                ->badge(\App\Models\Attendance::where('status', 'absent')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'absent')),
            'late' => \Filament\Resources\Pages\ListRecords\Tab::make('Late')
                ->badge(\App\Models\Attendance::where('status', 'late')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'late')),
        ];
    }
}
