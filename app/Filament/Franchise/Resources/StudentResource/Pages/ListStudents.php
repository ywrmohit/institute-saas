<?php

namespace App\Filament\Franchise\Resources\StudentResource\Pages;

use App\Filament\Franchise\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Admission'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Students')
                ->badge(fn() => static::getResource()::getEloquentQuery()->count()),

            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active'))
                ->badge(fn() => static::getResource()::getEloquentQuery()->where('status', 'active')->count())
                ->badgeColor('success'),

            'defaulters' => Tab::make('Attendance Defaulters (<75%)')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('attendances')->whereRaw('(SELECT (COUNT(CASE WHEN status IN ("present", "late") THEN 1 END) / COUNT(*)) * 100 FROM attendances WHERE attendances.student_id = students.id) < 75');
                })
                ->badge(function () {
                    return static::getResource()::getEloquentQuery()
                        ->whereHas('attendances')
                        ->whereRaw('(SELECT (COUNT(CASE WHEN status IN ("present", "late") THEN 1 END) / COUNT(*)) * 100 FROM attendances WHERE attendances.student_id = students.id) < 75')
                        ->count();
                })
                ->badgeColor('danger'),

            'fee_due' => Tab::make('Fee Dues / Defaulters')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('feeInvoices', fn($q) => $q->where('pending_amount', '>', 0)))
                ->badge(fn() => static::getResource()::getEloquentQuery()->whereHas('feeInvoices', fn($q) => $q->where('pending_amount', '>', 0))->count())
                ->badgeColor('warning'),

            'alumni' => Tab::make('Alumni / Graduated')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                ->badge(fn() => static::getResource()::getEloquentQuery()->where('status', 'completed')->count())
                ->badgeColor('info'),
        ];
    }
}
