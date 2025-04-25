<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadsCountWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 6;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Leads', Lead::count())
                ->description('All time leads')
                ->color('primary')
        ];
    }
}
