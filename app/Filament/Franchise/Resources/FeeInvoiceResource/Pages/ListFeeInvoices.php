<?php

namespace App\Filament\Franchise\Resources\FeeInvoiceResource\Pages;

use App\Filament\Franchise\Resources\FeeInvoiceResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListFeeInvoices extends ListRecords
{
    protected static string $resource = FeeInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Fee Invoice'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Invoices')
                ->badge(fn() => static::getResource()::getEloquentQuery()->count()),

            'paid' => Tab::make('Paid in Full')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'paid'))
                ->badge(fn() => static::getResource()::getEloquentQuery()->where('status', 'paid')->count())
                ->badgeColor('success'),

            'partially_paid' => Tab::make('Partially Paid')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'partially_paid'))
                ->badge(fn() => static::getResource()::getEloquentQuery()->where('status', 'partially_paid')->count())
                ->badgeColor('warning'),

            'overdue' => Tab::make('Overdue Dues')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('pending_amount', '>', 0)->where('due_date', '<', today()))
                ->badge(fn() => static::getResource()::getEloquentQuery()->where('pending_amount', '>', 0)->where('due_date', '<', today())->count())
                ->badgeColor('danger'),

            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                ->badge(fn() => static::getResource()::getEloquentQuery()->where('status', 'pending')->count())
                ->badgeColor('gray'),
        ];
    }
}
