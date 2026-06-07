<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('coupon.name')
                    ->searchable(),
                TextColumn::make('delivery_slot_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('order_number')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->searchable(),
                TextColumn::make('delivery_status')
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->searchable(),
                TextColumn::make('company_trn')
                    ->searchable(),
                TextColumn::make('emirate')
                    ->searchable(),
                TextColumn::make('subtotal')
                    ->money('AED')
                    ->sortable(),
                TextColumn::make('vat_total')
                    ->money('AED')
                    ->sortable(),
                TextColumn::make('shipping_fee')
                    ->money('AED')
                    ->sortable(),
                TextColumn::make('cod_fee')
                    ->money('AED')
                    ->sortable(),
                TextColumn::make('discount_total')
                    ->money('AED')
                    ->sortable(),
                TextColumn::make('total')
                    ->money('AED')
                    ->sortable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('payment_method')
                    ->searchable(),
                TextColumn::make('estimated_delivery_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('idempotency_key')
                    ->searchable(),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('confirmed_at')
                    ->dateTime()
                    ->sortable(),
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
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
