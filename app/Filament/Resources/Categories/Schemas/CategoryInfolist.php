<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('parent.name')
                    ->label('Parent')
                    ->state(fn (?Category $record): string => $record?->parent?->localized('name') ?? '')
                    ->placeholder('-'),
                TextEntry::make('name')
                    ->state(fn (?Category $record): string => $record?->localized('name') ?? '')
                    ->columnSpanFull(),
                TextEntry::make('slug'),
                TextEntry::make('description')
                    ->state(fn (?Category $record): string => $record?->localized('description') ?? '')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('image_path')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('meta_title')
                    ->state(fn (?Category $record): string => $record?->localized('meta_title') ?? '')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('meta_description')
                    ->state(fn (?Category $record): string => $record?->localized('meta_description') ?? '')
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
                    ->visible(fn (Category $record): bool => $record->trashed()),
            ]);
    }
}
