<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlightResource\Pages;
use App\Filament\Resources\FlightResource\RelationManagers;
use App\Models\Flight;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlightResource extends Resource
{
    protected static ?string $model = Flight::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-right';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Flight Information')
                        ->schema([
                            TextInput::make('flight_number')->required()->unique(ignoreRecord: true),
                            Select::make('airline_id')->relationship('airline', 'name')->required(),
                        ]),
                    Wizard\Step::make('Flight Segments')
                        ->schema([
                            Repeater::make('flight_segments')->relationship('segments')->schema([
                                TextInput::make('sequence')->numeric()->required(),
                                Select::make('airport_id')->relationship('airport', 'name')->required(),
                                DateTimePicker::make('time')->required()
                            ])->collapsed(false)->minItems(1)
                        ]),
                    Wizard\Step::make('Flight Class')
                        ->schema([
                            Repeater::make('flight_classes')->relationship('classes')->schema([
                                Select::make('class_type')->options([
                                    'bussiness' => 'Bussiness',
                                    'economy' => 'Economy'
                                ])->required(),
                                TextInput::make('price')->required()->prefix('IDR')->numeric()->minValue(0)->label('Price'),
                                TextInput::make('total_seats')->required()->numeric()->minValue(1)->label('Total Seats'),
                                Select::make('facilities')->relationship('facilities', 'name')->multiple()->required()
                            ])
                        ]),
                ])->columnSpan(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('flight_number'),
                TextColumn::make('airline.name'),
                TextColumn::make('segments')->label('Route & Duration')->formatStateUsing(function (Flight $record): string {
                    $firstSegment = $record->segments->first();
                    $lastSegment = $record->segments->last();
                    $route = $firstSegment->airport->iata_code . ' - ' . $lastSegment->airport->iata_code;
                    $duration = (new DateTime($firstSegment->time))->format('d F Y H:i') . ' -' . (new DateTime($lastSegment->time))->format('d F Y H:i');
                    return $route . ' | ' . $duration;
                }),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlights::route('/'),
            'create' => Pages\CreateFlight::route('/create'),
            'edit' => Pages\EditFlight::route('/{record}/edit'),
        ];
    }
}
