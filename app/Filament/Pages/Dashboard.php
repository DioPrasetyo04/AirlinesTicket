<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\DashboardOverview;

class Dashboard extends BaseDashboard
{
    public function getHeaderWidgets(): array
    {
        return [
            DashboardOverview::class,
        ];
    }

    public function getWidgets(): array
    {
        return [];
    }
}
