<?php

namespace App\Filament\Resources\DeliverySlots\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DeliverySlotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('shipping_zone_id')
                    ->relationship('shippingZone', 'id'),
                TextInput::make('label.en')
                    ->label('Label (English)')
                    ->required(),
                TextInput::make('label.ar')
                    ->label('Label (Arabic)')
                    ->required()
                    ->columnSpanFull(),
                TimePicker::make('starts_at')
                    ->required(),
                TimePicker::make('ends_at')
                    ->required(),
                TimePicker::make('cutoff_time'),
                Toggle::make('is_same_day')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
