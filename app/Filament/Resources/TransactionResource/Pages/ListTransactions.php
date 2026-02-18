<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Filament\Widgets\TransactionOverview;
use Filament\Actions;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    // menambahkan widget card pada header section stat dari Transaction Overview Widget
    public function getHeaderWidgets(): array
    {
        return [
            TransactionOverview::class
        ];
    }

    // header section untuk create form data
    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }


}
