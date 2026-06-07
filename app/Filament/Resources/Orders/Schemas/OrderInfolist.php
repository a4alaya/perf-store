<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User')
                    ->placeholder('-'),
                TextEntry::make('coupon.name')
                    ->label('Coupon')
                    ->placeholder('-'),
                TextEntry::make('delivery_slot_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('order_number'),
                TextEntry::make('status'),
                TextEntry::make('payment_status'),
                TextEntry::make('delivery_status'),
                TextEntry::make('customer_name'),
                TextEntry::make('customer_email'),
                TextEntry::make('customer_phone'),
                TextEntry::make('company_trn')
                    ->placeholder('-'),
                TextEntry::make('emirate'),
                TextEntry::make('shipping_address')
                    ->columnSpanFull(),
                TextEntry::make('billing_address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('subtotal')
                    ->numeric(),
                TextEntry::make('vat_total')
                    ->numeric(),
                TextEntry::make('shipping_fee')
                    ->numeric(),
                TextEntry::make('cod_fee')
                    ->numeric(),
                TextEntry::make('discount_total')
                    ->numeric(),
                TextEntry::make('total')
                    ->numeric(),
                TextEntry::make('currency'),
                TextEntry::make('payment_method'),
                TextEntry::make('customer_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('admin_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('estimated_delivery_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('idempotency_key')
                    ->placeholder('-'),
                TextEntry::make('paid_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('confirmed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
