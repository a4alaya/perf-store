<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name.en')
                    ->label('Name (English)')
                    ->columnSpanFull(),
                TextInput::make('name.ar')
                    ->label('Name (Arabic)')
                    ->columnSpanFull(),
                Select::make('type')
                    ->options(['percentage' => 'Percentage', 'fixed' => 'Fixed amount', 'free_shipping' => 'Free shipping'])
                    ->required(),
                TextInput::make('value')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('min_order_amount')
                    ->prefix('AED')
                    ->numeric(),
                TextInput::make('usage_limit')
                    ->numeric(),
                TextInput::make('usage_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('per_customer_limit')
                    ->numeric(),
                TextInput::make('customer_user_id')
                    ->numeric(),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('expires_at'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
