<?php

namespace App\Filament\Franchise\Widgets;

use App\Models\Attendance;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\FeeInvoice;
use App\Models\Payment;
use App\Models\Student;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FranchiseStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        $franchiseId = $tenant?->id;

        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $totalBranches = Branch::count();
        $activeBatches = Batch::where('status', 'active')->count();

        $totalRevenue = Payment::sum('amount');
        $totalPending = FeeInvoice::sum('pending_amount');

        $todayAttendancePresent = Attendance::whereDate('date', today())->where('status', 'present')->count();
        $todayAttendanceTotal = Attendance::whereDate('date', today())->count();
        $attendanceRate = $todayAttendanceTotal > 0 ? round(($todayAttendancePresent / $todayAttendanceTotal) * 100, 1) : 0;

        return [
            Stat::make('Active Students', $activeStudents)
                ->description("{$totalStudents} total enrolled")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Institute Branches', $totalBranches)
                ->description('Operating centers')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('info'),

            Stat::make('Running Batches', $activeBatches)
                ->description('Classrooms active')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Total Fees Collected', '₹' . number_format($totalRevenue, 2))
                ->description('Received payments')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pending Fee Balance', '₹' . number_format($totalPending, 2))
                ->description('Outstanding receivables')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($totalPending > 0 ? 'danger' : 'gray'),

            Stat::make("Today's Attendance", "{$attendanceRate}%")
                ->description("{$todayAttendancePresent} / {$todayAttendanceTotal} present today")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($attendanceRate >= 75 ? 'success' : 'warning'),
        ];
    }
}
