<?php

namespace App\Filament\Resources\ShippingZones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ShippingZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('emirate')
                    ->required(),
                TextInput::make('delivery_fee')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('free_shipping_threshold')
                    ->numeric(),
                Toggle::make('same_day_available')
                    ->required(),
                Toggle::make('next_day_available')
                    ->required(),
                TextInput::make('cod_fee')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('min_order_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('estimated_days_min')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('estimated_days_max')
                    ->required()
                    ->numeric()
                    ->default(3),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
