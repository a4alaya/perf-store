<?php

namespace App\Filament\Resources\PaymentLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PaymentLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('payment_id')
                    ->relationship('payment', 'id'),
                Select::make('order_id')
                    ->relationship('order', 'id'),
                TextInput::make('gateway')
                    ->required(),
                TextInput::make('event_type'),
                TextInput::make('gateway_event_id'),
                TextInput::make('status'),
                Textarea::make('payload')
                    ->columnSpanFull(),
                DateTimePicker::make('received_at'),
            ]);
    }
}
