<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('brand.name')
                    ->label('Brand')
                    ->state(fn (?Product $record): string => $record?->brand?->localized('name') ?? ''),
                TextEntry::make('category.name')
                    ->label('Category')
                    ->state(fn (?Product $record): string => $record?->category?->localized('name') ?? ''),
                TextEntry::make('name')
                    ->state(fn (?Product $record): string => $record?->localized('name') ?? '')
                    ->columnSpanFull(),
                TextEntry::make('slug'),
                TextEntry::make('sku')
                    ->label('SKU'),
                TextEntry::make('gender'),
                TextEntry::make('type'),
                TextEntry::make('short_description')
                    ->state(fn (?Product $record): string => $record?->localized('short_description') ?? '')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('description')
                    ->state(fn (?Product $record): string => $record?->localized('description') ?? '')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('top_notes')
                    ->state(fn (?Product $record): string => self::formatNotes($record?->top_notes, app()->getLocale()))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('middle_notes')
                    ->state(fn (?Product $record): string => self::formatNotes($record?->middle_notes, app()->getLocale()))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('base_notes')
                    ->state(fn (?Product $record): string => self::formatNotes($record?->base_notes, app()->getLocale()))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('price')
                    ->money(),
                TextEntry::make('sale_price')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('stock_quantity')
                    ->numeric(),
                ImageEntry::make('featured_image_path')
                    ->placeholder('-'),
                IconEntry::make('is_featured')
                    ->boolean(),
                IconEntry::make('is_best_seller')
                    ->boolean(),
                IconEntry::make('is_new_arrival')
                    ->boolean(),
                IconEntry::make('is_uae_exclusive')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('vat_taxable')
                    ->boolean(),
                TextEntry::make('weight_grams')
                    ->numeric(),
                TextEntry::make('average_rating')
                    ->numeric(),
                TextEntry::make('reviews_count')
                    ->numeric(),
                TextEntry::make('meta_title')
                    ->state(fn (?Product $record): string => $record?->localized('meta_title') ?? '')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('meta_description')
                    ->state(fn (?Product $record): string => $record?->localized('meta_description') ?? '')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('published_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Product $record): bool => $record->trashed()),
            ]);
    }

    private static function formatNotes(array|null $notes, string $locale): string
    {
        if (! is_array($notes)) {
            return '';
        }

        $values = $notes[$locale] ?? $notes[config('store.default_locale', 'en')] ?? reset($notes);

        if (! is_array($values)) {
            return (string) $values;
        }

        return implode(', ', array_filter($values, fn ($value) => filled($value)));
    }
}
