<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Lead;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class UnifiedWidgets extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $role = $user->role;
        $stats = [];

        $leadsCount = match ($role) {
            'admin' => Lead::count(),
            'manager' => Lead::whereHas('user', fn($q) => $q->where('team_lead_id', $user->id))
                ->count(),
            'center' => Lead::where('user_id', $user->id)
                ->count(),
            'agent' => Lead::where('assigned_to_id', $user->id)
                ->count(),
            default => 0,
        };

        $paidLeadsCount = match ($role) {
            'admin' => Lead::whereHas('status', fn($q) => $q->where('name', 'paid'))
                ->count(),
            'manager' => Lead::whereHas('user', fn($q) => $q->where('team_lead_id', $user->id))
                ->whereHas('status', fn($q) => $q->where('name', 'paid'))
                ->count(),
            'center' => Lead::where('user_id', $user->id)
                ->whereHas('status', fn($q) => $q->where('name', 'paid'))->where('status', 'paid')
                ->count(),
            'agent' => Lead::where('assigned_to_id', $user->id)
                ->whereHas('status', fn($q) => $q->where('name', 'paid'))
                ->count(),
            default => 0,
        };

        $stats = [
            Stat::make('Total Leads', $leadsCount)
                ->description(
                    Lead::when(
                        Lead::exists(),
                        fn($query) => 'Last Lead Posted on ' . $query->latest()->first()->created_at->format('d M Y'),
                        fn() => 'No leads have been posted yet'
                    )
                )
                ->color('gray')
                ->chart([1, 4, 5, 2, 3, 4, 5])
                ->descriptionIcon('heroicon-o-user-group'),
            Stat::make('Paid Leads', $paidLeadsCount)
                ->description(
                    Lead::whereHas('status', fn($q) => $q->where('name', 'paid'))
                        ->when(
                            fn($query) => $query->exists(),
                            fn($query) => 'Last Lead Paid on ' . $query->latest()->first()->created_at->format('d M Y'),
                            fn() => 'No paid leads yet'
                        )
                )
                ->color('success')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([1, 4, 5, 2, 3, 4, 5]),
        ];

        if ($role == 'admin' || $role == 'manager') {
            $stats[] = Stat::make('Total Centers', User::where('role', 'center')->count())
                ->description('All time centers')
                ->color('warning')
                ->descriptionIcon('heroicon-o-building-office-2')
                ->chart([1, 4, 5, 2, 3, 4, 5]);
        }

        return $stats;
    }
}
