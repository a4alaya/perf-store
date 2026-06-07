<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Brand;
use App\Models\Category;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('brand_id')
                    ->options(fn () => Brand::query()->get()->mapWithKeys(fn (Brand $brand) => [$brand->id => $brand->localized('name')]))
                    ->searchable()
                    ->required(),
                Select::make('category_id')
                    ->options(fn () => Category::query()->get()->mapWithKeys(fn (Category $category) => [$category->id => $category->localized('name')]))
                    ->searchable()
                    ->required(),
                TextInput::make('name.en')
                    ->label('Name (English)')
                    ->required(),
                TextInput::make('name.ar')
                    ->label('Name (Arabic)'),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                Select::make('gender')
                    ->options(['men' => 'Men', 'women' => 'Women', 'unisex' => 'Unisex'])
                    ->required()
                    ->default('unisex'),
                Select::make('type')
                    ->options([
                        'perfume' => 'Perfume',
                        'oud' => 'Oud',
                        'bakhoor' => 'Bakhoor',
                        'attar' => 'Attar',
                        'gift_set' => 'Gift Set',
                    ])
                    ->required()
                    ->default('perfume'),
                Textarea::make('short_description.en')
                    ->label('Short description (English)')
                    ->columnSpanFull(),
                Textarea::make('short_description.ar')
                    ->label('Short description (Arabic)')
                    ->columnSpanFull(),
                Textarea::make('description.en')
                    ->label('Full description (English)')
                    ->columnSpanFull(),
                Textarea::make('description.ar')
                    ->label('Full description (Arabic)')
                    ->columnSpanFull(),
                KeyValue::make('top_notes.en')
                    ->label('Top notes (English)')
                    ->columnSpanFull(),
                KeyValue::make('top_notes.ar')
                    ->label('Top notes (Arabic)')
                    ->columnSpanFull(),
                KeyValue::make('middle_notes.en')
                    ->label('Middle notes (English)')
                    ->columnSpanFull(),
                KeyValue::make('middle_notes.ar')
                    ->label('Middle notes (Arabic)')
                    ->columnSpanFull(),
                KeyValue::make('base_notes.en')
                    ->label('Base notes (English)')
                    ->columnSpanFull(),
                KeyValue::make('base_notes.ar')
                    ->label('Base notes (Arabic)')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('AED'),
                TextInput::make('sale_price')
                    ->numeric()
                    ->prefix('AED'),
                TextInput::make('stock_quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                FileUpload::make('featured_image_path')
                    ->disk('public')
                    ->directory('products')
                    ->image(),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('is_best_seller')
                    ->required(),
                Toggle::make('is_new_arrival')
                    ->required(),
                Toggle::make('is_uae_exclusive')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('vat_taxable')
                    ->required(),
                TextInput::make('weight_grams')
                    ->required()
                    ->numeric()
                    ->default(500),
                TextInput::make('average_rating')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('reviews_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('meta_title.en')
                    ->label('Meta title (English)')
                    ->columnSpanFull(),
                TextInput::make('meta_title.ar')
                    ->label('Meta title (Arabic)')
                    ->columnSpanFull(),
                Textarea::make('meta_description.en')
                    ->label('Meta description (English)')
                    ->columnSpanFull(),
                Textarea::make('meta_description.ar')
                    ->label('Meta description (Arabic)')
                    ->columnSpanFull(),
                DateTimePicker::make('published_at'),
            ]);
    }
}
