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
        $user = auth()->user();

        // 1. Faculty / Trainer Workspace Stats
        if ($user && $user->isTrainer()) {
            $myBatchIds = Batch::where('trainer_id', $user->id)->pluck('id');
            $myStudentsCount = Student::whereHas('enrollments', fn($q) => $q->whereIn('batch_id', $myBatchIds))->count();
            $myBatchesCount = $myBatchIds->count();
            $todayPresent = Attendance::whereIn('batch_id', $myBatchIds)->whereDate('date', today())->where('status', 'present')->count();
            $todayTotal = Attendance::whereIn('batch_id', $myBatchIds)->whereDate('date', today())->count();
            $myRate = $todayTotal > 0 ? round(($todayPresent / $todayTotal) * 100, 1) : 0;
            $myMaterials = \App\Models\StudyMaterial::where(fn($q) => $q->whereIn('batch_id', $myBatchIds)->orWhereNull('batch_id'))->count();

            return [
                Stat::make('My Enrolled Students', $myStudentsCount)
                    ->description('Under your instruction')
                    ->descriptionIcon('heroicon-m-academic-cap')
                    ->color('primary'),

                Stat::make('Assigned Batches', $myBatchesCount)
                    ->description('Active classrooms')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color('info'),

                Stat::make("Class Attendance Today", "{$myRate}%")
                    ->description("{$todayPresent} / {$todayTotal} marked present")
                    ->descriptionIcon('heroicon-m-calendar-days')
                    ->color($myRate >= 75 ? 'success' : 'warning'),

                Stat::make('Course Study Materials', $myMaterials)
                    ->description('Curriculum & handouts')
                    ->descriptionIcon('heroicon-m-book-open')
                    ->color('secondary'),
            ];
        }

        // 2. Financial Accountant Workspace Stats
        if ($user && $user->isAccountant()) {
            $totalRevenue = Payment::sum('amount');
            $totalPending = FeeInvoice::sum('pending_amount');
            $overdueInvoices = FeeInvoice::where('status', 'overdue')->orWhere(fn($q) => $q->where('due_date', '<', today())->where('pending_amount', '>', 0))->count();
            $todayCollections = Payment::whereDate('payment_date', today())->sum('amount');

            return [
                Stat::make("Today's Collections", '₹' . number_format($todayCollections, 2))
                    ->description('Cash & digital received today')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color('success'),

                Stat::make('Total Revenue Collected', '₹' . number_format($totalRevenue, 2))
                    ->description('Cumulative institute ledger')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('primary'),

                Stat::make('Total Pending Receivables', '₹' . number_format($totalPending, 2))
                    ->description('Uncollected fee balance')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color($totalPending > 0 ? 'danger' : 'gray'),

                Stat::make('Overdue Fee Invoices', $overdueInvoices)
                    ->description('Defaulters needing follow-up')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color($overdueInvoices > 0 ? 'danger' : 'success'),
            ];
        }

        // 3. Branch Admin Workspace Stats
        if ($user && $user->isBranchAdmin()) {
            $branchId = $user->branch_id;
            $studentsQuery = Student::when($branchId, fn($q) => $q->where('branch_id', $branchId));
            $activeStudents = (clone $studentsQuery)->where('status', 'active')->count();
            $branchBatches = Batch::when($branchId, fn($q) => $q->where('branch_id', $branchId))->where('status', 'active')->count();
            $branchRevenue = Payment::when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('amount');
            $branchPending = FeeInvoice::when($branchId, fn($q) => $q->where('branch_id', $branchId))->sum('pending_amount');

            return [
                Stat::make('Branch Active Students', $activeStudents)
                    ->description('Campus enrolled students')
                    ->descriptionIcon('heroicon-m-academic-cap')
                    ->chart([1, 2, 2, 3, 3, $activeStudents])
                    ->color('success'),

                Stat::make('Branch Active Batches', $branchBatches)
                    ->description('Campus classrooms running')
                    ->descriptionIcon('heroicon-m-clock')
                    ->chart([1, 1, 2, 2, $branchBatches])
                    ->color('info'),

                Stat::make('Branch Fees Collected', '₹' . number_format($branchRevenue, 2))
                    ->description('Campus collections')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->chart([10000, 20000, 35000, 45000, $branchRevenue])
                    ->color('success'),

                Stat::make('Branch Fee Receivables', '₹' . number_format($branchPending, 2))
                    ->description('Outstanding dues at branch')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->chart([50000, 40000, 30000, $branchPending])
                    ->color($branchPending > 0 ? 'warning' : 'gray'),
            ];
        }

        // 4. Franchise Owner / Super Admin Global Stats
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $thisMonthAdmissions = Student::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $activeBatches = Batch::where('status', 'active')->count();

        $totalRevenue = Payment::sum('amount');
        $totalPending = FeeInvoice::sum('pending_amount');
        $overdueInvoices = FeeInvoice::where('status', 'overdue')
            ->orWhere(fn($q) => $q->where('due_date', '<', today())->where('pending_amount', '>', 0))
            ->count();

        $todayAttendancePresent = Attendance::whereDate('date', today())->where('status', 'present')->count();
        $todayAttendanceTotal = Attendance::whereDate('date', today())->count();
        $attendanceRate = $todayAttendanceTotal > 0 ? round(($todayAttendancePresent / $todayAttendanceTotal) * 100, 1) : 0;

        return [
            Stat::make('Active Students', $activeStudents)
                ->description("{$thisMonthAdmissions} new this month · {$totalStudents} total")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->chart([1, 2, 2, 3, 3, $activeStudents])
                ->color('success'),

            Stat::make('Running Batches', $activeBatches)
                ->description('Classrooms active')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([1, 1, 2, 2, $activeBatches])
                ->color('info'),

            Stat::make("Today's Attendance", "{$attendanceRate}%")
                ->description("{$todayAttendancePresent} / {$todayAttendanceTotal} marked present")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart([70, 75, 80, 85, (int) $attendanceRate])
                ->color($attendanceRate >= 75 ? 'success' : ($attendanceRate > 0 ? 'warning' : 'gray')),

            Stat::make('Total Fees Collected', '₹' . number_format($totalRevenue, 2))
                ->description('Received payments')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([15000, 25000, 40000, 50000, (int) $totalRevenue])
                ->color('success'),

            Stat::make('Pending Fee Balance', '₹' . number_format($totalPending, 2))
                ->description('Outstanding receivables')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->chart([250000, 230000, 215000, (int) $totalPending])
                ->color($totalPending > 0 ? 'warning' : 'gray'),

            Stat::make('Overdue Invoices', $overdueInvoices)
                ->description($overdueInvoices > 0 ? 'Defaulters needing follow-up' : 'All accounts up to date')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color($overdueInvoices > 0 ? 'danger' : 'success'),
        ];
    }
}
