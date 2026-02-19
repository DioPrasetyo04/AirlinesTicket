<?php

namespace App\Filament\Widgets;

use App\Models\Airline;
use App\Models\Airport;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardOverview extends BaseWidget
{
    // protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 2; // ⭐ ubah jumlah grid jadi 2
    }

    protected function getStats(): array
    {
        $airlines = Airline::select('created_at')
            ->where('created_at', '>=', now()->subDays(6))
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m-d'));

        $countAirlinePerDay = collect(range(0, 6))->map(function ($day) use ($airlines) {
            $date = now()->subDays($day)->format('Y-m-d');
            $items = $airlines->get($date);
            return $items ? $items->count() : 0;
        })->values()->toArray();

        $airports = Airport::select('created_at')->where('created_at', '>=', now()->subDays(6))->get()->groupBy(fn($item) => $item->created_at->format('Y-m-d'));

        $countAirportPerDay = collect(range(0, 6))->map(function ($day) use ($airports) {
            $date = now()->subDays($day)->format('Y-m-d');
            $items = $airports->get($date);
            return $items ? $items->count() : 0;
        })->values()->toArray();

        return [
            Stat::make('Total Airlines', Airline::count())
                ->description('Last 6 days')
                ->descriptionIcon('heroicon-o-building-library')
                ->color('success')
                ->icon('heroicon-o-building-library')
                ->chart($countAirlinePerDay),
            Stat::make('Total Airports', Airport::count())
                ->description('Last 6 days')
                ->descriptionIcon('heroicon-o-building-library')
                ->color('warning')
                ->icon('heroicon-o-building-library')
                ->chart($countAirportPerDay),
        ];
    }
}
