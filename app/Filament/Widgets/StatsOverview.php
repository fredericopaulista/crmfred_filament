<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Quote;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Lead::count())
                ->description('Total leads in system')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            
            Stat::make('New Leads', Lead::where('created_at', '>=', now()->subDays(30))->count())
                ->description('Last 30 days')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Open Deals', Quote::where('status', 'sent')->count())
                ->description('Quotes sent')
                ->color('warning'),

            Stat::make('Revenue', 'R$ ' . number_format(Transaction::where('type', 'income')->sum('amount'), 2, ',', '.'))
                ->description('Total income')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
