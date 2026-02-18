<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Umum')
                    ->schema([
                        TextInput::make('code'),
                        Select::make('flight_id')->relationship('flight', 'flight_number'),
                        Select::make('flight_class_id')->relationship('class', 'class_type')->columnSpan(2)
                    ])->columns(2),
                Section::make('Informasi Penumpang')->schema([
                    TextInput::make('number_of_passengers'),
                    TextInput::make('name'),
                    TextInput::make('email'),
                    TextInput::make('phone'),
                    Section::make('Daftar Penumpang')->schema([
                        Repeater::make('Passengers')->relationship('passengers')->schema([
                            TextInput::make('seat.name'),
                            TextInput::make('name'),
                            TextInput::make('date_of_birth'),
                            TextInput::make('nationality')
                        ])->columns(2),
                    ])->columnSpan(2),
                ])->columns(2),
                Section::make('Pembayaran')->schema([
                    TextInput::make('promo.code'),
                    TextInput::make('promo.discount_type'),
                    TextInput::make('promo.discount'),
                    TextInput::make('payment_status'),
                    TextInput::make('subtotal'),
                    TextInput::make('grandtotal'),
                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code'),
                TextColumn::make('flight.flight_number'),
                TextColumn::make('number_of_passengers'),
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('phone'),
                TextColumn::make('promo.code'),
                TextColumn::make('payment_status'),
                TextColumn::make('subtotal'),
                TextColumn::make('grandtotal')
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ViewAction::make(),
                DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
