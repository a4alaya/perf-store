<?php

namespace App\Filament\Resources\ShippingZones\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShippingZonesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('emirate')
                    ->searchable(),
                TextColumn::make('delivery_fee')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('free_shipping_threshold')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('same_day_available')
                    ->boolean(),
                IconColumn::make('next_day_available')
                    ->boolean(),
                TextColumn::make('cod_fee')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('min_order_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estimated_days_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estimated_days_max')
                    ->numeric()
                    ->sortable(),
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
