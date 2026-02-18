<?php

namespace App\Filament\Resources\FlightResource\Pages;

use App\Filament\Resources\FlightResource;
use App\Models\Flight;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFlight extends CreateRecord
{
    protected static string $resource = FlightResource::class;

    // generate seat static method dari model Flight seperti observer pada laravel

    protected function afterCreate()
    {
        $flight = Flight::find($this->record->id);

        $flight->generateSeats();
    }
}
