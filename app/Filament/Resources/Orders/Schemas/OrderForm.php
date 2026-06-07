<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name'),
                Select::make('coupon_id')
                    ->relationship('coupon', 'name'),
                TextInput::make('delivery_slot_id')
                    ->numeric(),
                TextInput::make('order_number')
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending_payment' => 'Pending payment',
                        'payment_failed' => 'Payment failed',
                        'paid' => 'Paid',
                        'processing' => 'Processing',
                        'packed' => 'Packed',
                        'out_for_delivery' => 'Out for delivery',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                        'partially_refunded' => 'Partially refunded',
                    ])
                    ->required()
                    ->default('pending_payment'),
                Select::make('payment_status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded', 'cod_pending' => 'COD pending'])
                    ->required()
                    ->default('pending'),
                Select::make('delivery_status')
                    ->options(['not_dispatched' => 'Not dispatched', 'processing' => 'Processing', 'packed' => 'Packed', 'out_for_delivery' => 'Out for delivery', 'delivered' => 'Delivered'])
                    ->required()
                    ->default('not_dispatched'),
                TextInput::make('customer_name')
                    ->required(),
                TextInput::make('customer_email')
                    ->email()
                    ->required(),
                TextInput::make('customer_phone')
                    ->tel()
                    ->required(),
                TextInput::make('company_trn'),
                TextInput::make('emirate')
                    ->required(),
                KeyValue::make('shipping_address')
                    ->required()
                    ->columnSpanFull(),
                KeyValue::make('billing_address')
                    ->columnSpanFull(),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->prefix('AED'),
                TextInput::make('vat_total')
                    ->required()
                    ->numeric()
                    ->prefix('AED'),
                TextInput::make('shipping_fee')
                    ->required()
                    ->numeric()
                    ->prefix('AED')
                    ->default(0),
                TextInput::make('cod_fee')
                    ->required()
                    ->numeric()
                    ->prefix('AED')
                    ->default(0),
                TextInput::make('discount_total')
                    ->required()
                    ->numeric()
                    ->prefix('AED')
                    ->default(0),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->prefix('AED'),
                TextInput::make('currency')
                    ->required()
                    ->default('AED'),
                TextInput::make('payment_method')
                    ->required()
                    ->default('card'),
                Textarea::make('customer_notes')
                    ->columnSpanFull(),
                Textarea::make('admin_notes')
                    ->columnSpanFull(),
                DatePicker::make('estimated_delivery_date'),
                TextInput::make('idempotency_key'),
                DateTimePicker::make('paid_at'),
                DateTimePicker::make('confirmed_at'),
            ]);
    }
}
