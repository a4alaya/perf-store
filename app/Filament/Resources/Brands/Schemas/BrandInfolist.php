<?php

namespace App\Filament\Resources\Brands\Schemas;

use App\Models\Brand;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BrandInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->state(fn (?Brand $record): string => $record?->localized('name') ?? '')
                    ->columnSpanFull(),
                TextEntry::make('slug'),
                TextEntry::make('description')
                    ->state(fn (?Brand $record): string => $record?->localized('description') ?? '')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('logo_path')
                    ->placeholder('-'),
                TextEntry::make('country')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('meta_title')
                    ->state(fn (?Brand $record): string => $record?->localized('meta_title') ?? '')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('meta_description')
                    ->state(fn (?Brand $record): string => $record?->localized('meta_description') ?? '')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Brand $record): bool => $record->trashed()),
            ]);
    }
}
