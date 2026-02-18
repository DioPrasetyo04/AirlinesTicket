<?php

namespace App\Filament\Resources\FlightResource\Pages;

use App\Filament\Resources\FlightResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlight extends EditRecord
{
    protected static string $resource = FlightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // observer ketika edit auto generate Seats ketika bussines dan total seats diubah di model FLight
    protected function afterSave()
    {
        $this->record->generateSeats();
    }
}
