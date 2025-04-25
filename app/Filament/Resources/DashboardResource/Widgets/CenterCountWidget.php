<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CenterCountWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 6;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Centers', User::where('role', 'center')->count())
                ->description('All time centers')
                ->color('success')
        ];
    }
}
