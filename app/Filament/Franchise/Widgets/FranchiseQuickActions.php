<?php

namespace App\Filament\Franchise\Widgets;

use App\Filament\Franchise\Resources\AnnouncementResource;
use App\Filament\Franchise\Resources\AttendanceResource;
use App\Filament\Franchise\Resources\PaymentResource;
use App\Filament\Franchise\Resources\StudentResource;
use Filament\Widgets\Widget;

class FranchiseQuickActions extends Widget
{
    protected static ?int $sort = 0;
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'filament.franchise.widgets.franchise-quick-actions';

    protected function getViewData(): array
    {
        return [
            'actions' => $this->getQuickActions(),
        ];
    }

    public function getQuickActions(): array
    {
        $actions = [];
        $user = auth()->user();

        // 1. New Admission
        if ($user && ($user->isSuperAdmin() || $user->isFranchiseOwner() || $user->isBranchAdmin())) {
            $actions[] = [
                'label' => 'New Admission',
                'description' => 'Enroll a new student',
                'url' => StudentResource::getUrl('create'),
                'icon' => 'heroicon-m-user-plus',
                'color' => 'bg-blue-600 hover:bg-blue-700 text-white',
                'badge' => 'Admission',
            ];
        }

        // 2. Record Payment
        if ($user && ($user->isSuperAdmin() || $user->isFranchiseOwner() || $user->isBranchAdmin() || $user->isAccountant())) {
            $actions[] = [
                'label' => 'Collect Fee',
                'description' => 'Record installment / fee',
                'url' => PaymentResource::getUrl('create'),
                'icon' => 'heroicon-m-banknotes',
                'color' => 'bg-emerald-600 hover:bg-emerald-700 text-white',
                'badge' => 'Finance',
            ];
        }

        // 3. Mark Attendance
        if ($user && ($user->isSuperAdmin() || $user->isFranchiseOwner() || $user->isBranchAdmin() || $user->isTrainer())) {
            $actions[] = [
                'label' => 'Mark Attendance',
                'description' => 'Daily roll-call & sessions',
                'url' => AttendanceResource::getUrl('index'),
                'icon' => 'heroicon-m-calendar-days',
                'color' => 'bg-amber-600 hover:bg-amber-700 text-white',
                'badge' => 'Daily Ops',
            ];
        }

        // 4. Post Announcement
        if ($user && ($user->isSuperAdmin() || $user->isFranchiseOwner() || $user->isBranchAdmin())) {
            $actions[] = [
                'label' => 'Post Notice',
                'description' => 'Broadcast to students/staff',
                'url' => AnnouncementResource::getUrl('create'),
                'icon' => 'heroicon-m-megaphone',
                'color' => 'bg-purple-600 hover:bg-purple-700 text-white',
                'badge' => 'Broadcast',
            ];
        }

        return $actions;
    }
}
