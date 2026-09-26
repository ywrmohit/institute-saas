<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Course;
use App\Models\FeeInvoice;
use App\Models\Franchise;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SaaSDashboardOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalFranchises = Franchise::count();
        $activeFranchises = Franchise::where('status', 'active')->count();
        $totalBranches = Branch::count();
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $totalTrainers = User::where('role', 'trainer')->count();
        $totalCourses = Course::count();

        $totalRevenue = Payment::sum('amount');
        $totalPendingFees = FeeInvoice::sum('pending_amount');

        return [
            Stat::make('Total Franchises', $totalFranchises)
                ->description("{$activeFranchises} active franchises")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Total Branches', $totalBranches)
                ->description('Operating learning centers')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('info'),

            Stat::make('Total Enrolled Students', $totalStudents)
                ->description("{$activeStudents} currently active")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Platform Trainers', $totalTrainers)
                ->description('Certified faculty members')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),

            Stat::make('Total Course Offerings', $totalCourses)
                ->description('Diploma & certificate programs')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('gray'),

            Stat::make('Total Fee Collections', '₹' . number_format($totalRevenue, 2))
                ->description('Processed across all franchises')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pending Fee Receivables', '₹' . number_format($totalPendingFees, 2))
                ->description('Outstanding across all institutes')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}
