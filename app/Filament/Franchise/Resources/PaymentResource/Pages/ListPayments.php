<?php

namespace App\Filament\Franchise\Resources\PaymentResource\Pages;

use App\Filament\Franchise\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Record Fee Receipt'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Collections')
                ->badge(fn() => static::getResource()::getEloquentQuery()->count()),

            'today' => Tab::make("Today's Receipts")
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('payment_date', today()))
                ->badge(fn() => static::getResource()::getEloquentQuery()->whereDate('payment_date', today())->count())
                ->badgeColor('success'),

            'this_week' => Tab::make('This Week')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()]))
                ->badge(fn() => static::getResource()::getEloquentQuery()->whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()])->count())
                ->badgeColor('primary'),

            'cash' => Tab::make('Cash')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('payment_method', 'cash'))
                ->badge(fn() => static::getResource()::getEloquentQuery()->where('payment_method', 'cash')->count())
                ->badgeColor('warning'),

            'digital' => Tab::make('UPI / Bank')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('payment_method', ['upi', 'bank_transfer', 'online']))
                ->badge(fn() => static::getResource()::getEloquentQuery()->whereIn('payment_method', ['upi', 'bank_transfer', 'online'])->count())
                ->badgeColor('info'),
        ];
    }
}
