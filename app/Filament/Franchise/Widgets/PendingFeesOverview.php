<?php

namespace App\Filament\Franchise\Widgets;

use App\Filament\Franchise\Resources\FeeInvoiceResource;
use App\Filament\Franchise\Resources\PaymentResource;
use App\Models\FeeInvoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingFeesOverview extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isFranchiseOwner() || $user->isBranchAdmin() || $user->isAccountant());
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = auth()->user();
                $query = FeeInvoice::query()
                    ->where('pending_amount', '>', 0)
                    ->with(['student', 'branch'])
                    ->orderBy('due_date', 'asc');

                if ($user && $user->isBranchAdmin() && $user->branch_id) {
                    $query->where('branch_id', $user->branch_id);
                }

                return $query->limit(4);
            })
            ->heading('Pending Fees & Defaulters')
            ->description('Immediate dues requiring recovery')
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student')
                    ->weight('bold')
                    ->description(fn(FeeInvoice $record): string => $record->student?->phone ?? 'No phone')
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('pending_amount')
                    ->label('Pending Due')
                    ->money('INR')
                    ->weight('bold')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('d M Y')
                    ->badge()
                    ->color(fn(FeeInvoice $record): string => $record->due_date && $record->due_date->isPast() ? 'danger' : 'warning'),
            ])
            ->paginated(false)
            ->emptyStateHeading('No overdue fee invoices')
            ->emptyStateDescription('All student fee accounts are settled or up to date.')
            ->emptyStateIcon('heroicon-o-check-badge')
            ->actions([
                Tables\Actions\Action::make('record_payment')
                    ->label('Collect')
                    ->icon('heroicon-m-banknotes')
                    ->color('success')
                    ->button()
                    ->size('xs')
                    ->url(fn(FeeInvoice $record): string => PaymentResource::getUrl('create', [
                        'student_id' => $record->student_id,
                        'fee_invoice_id' => $record->id,
                    ])),
            ]);
    }
}
