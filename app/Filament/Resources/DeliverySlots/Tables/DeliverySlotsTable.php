<?php

namespace App\Filament\Resources\DeliverySlots\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeliverySlotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shippingZone.emirate')
                    ->label('Emirate')
                    ->searchable(),
                TextColumn::make('label')
                    ->formatStateUsing(fn ($record) => $record->localized('label'))
                    ->searchable(),
                TextColumn::make('starts_at')
                    ->time()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->time()
                    ->sortable(),
                TextColumn::make('cutoff_time')
                    ->time()
                    ->sortable(),
                IconColumn::make('is_same_day')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
