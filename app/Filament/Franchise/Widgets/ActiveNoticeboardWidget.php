<?php

namespace App\Filament\Franchise\Widgets;

use App\Filament\Franchise\Resources\AnnouncementResource;
use App\Models\Announcement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Str;

class ActiveNoticeboardWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = auth()->user();
                $query = Announcement::query()
                    ->where('is_active', true)
                    ->latest();

                if ($user && $user->isBranchAdmin() && $user->branch_id) {
                    $query->where(function ($q) use ($user) {
                        $q->where('branch_id', $user->branch_id)->orWhereNull('branch_id');
                    });
                }

                return $query->limit(4);
            })
            ->heading('Noticeboard & Announcements')
            ->description('Active campus communications')
            ->paginated(false)
            ->emptyStateHeading('No active announcements')
            ->emptyStateDescription('Click below to broadcast a notice to students or staff.')
            ->emptyStateIcon('heroicon-o-megaphone')
            ->emptyStateActions([
                Tables\Actions\Action::make('create_notice')
                    ->label('Post New Notice')
                    ->icon('heroicon-m-plus')
                    ->button()
                    ->url(fn(): string => AnnouncementResource::getUrl('create')),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Notice')
                    ->weight('bold')
                    ->description(fn(Announcement $record): string => Str::limit($record->content, 65))
                    ->wrap(),

                Tables\Columns\TextColumn::make('target_role')
                    ->label('Audience')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'all' => 'info',
                        'student' => 'success',
                        'trainer' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Posted')
                    ->date('d M Y')
                    ->color('gray'),
            ]);
    }
}
