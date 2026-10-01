<?php

namespace App\Filament\Franchise\Widgets;

use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class MonthlyRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue & Admissions Growth';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '290px';
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isFranchiseOwner() || $user->isBranchAdmin() || $user->isAccountant());
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $branchId = ($user && $user->isBranchAdmin()) ? $user->branch_id : null;

        $months = collect(range(5, 0))->map(function ($i) {
            return Carbon::now()->subMonths($i);
        });

        $labels = $months->map(fn($date) => $date->format('M'))->toArray();

        $revenueData = $months->map(function ($date) use ($branchId) {
            return (float) Payment::when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
        })->toArray();

        $admissionsData = $months->map(function ($date) use ($branchId) {
            return Student::when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Fees Collected (₹)',
                    'data' => $revenueData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'New Students',
                    'data' => $admissionsData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
