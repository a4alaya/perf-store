<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Models\Product;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->options(fn () => Product::query()
                        ->orderBy('id')
                        ->get()
                        ->mapWithKeys(fn (Product $product) => [$product->id => $product->localized('name').' ('.$product->sku.')']))
                    ->searchable()
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('order_id')
                    ->numeric(),
                TextInput::make('rating')
                    ->required()
                    ->numeric(),
                TextInput::make('title.en')
                    ->label('Title (English)'),
                TextInput::make('title.ar')
                    ->label('Title (Arabic)'),
                Textarea::make('body.en')
                    ->label('Body (English)')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('body.ar')
                    ->label('Body (Arabic)')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('images')
                    ->columnSpanFull(),
                Toggle::make('is_verified_purchase')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                Textarea::make('admin_response.en')
                    ->label('Admin response (English)')
                    ->columnSpanFull(),
                Textarea::make('admin_response.ar')
                    ->label('Admin response (Arabic)')
                    ->columnSpanFull(),
                DateTimePicker::make('reported_at'),
                DateTimePicker::make('approved_at'),
            ]);
    }
}
