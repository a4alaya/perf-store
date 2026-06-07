<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Product')
                    ->formatStateUsing(fn ($record) => $record->localized('name'))
                    ->searchable(),
                TextColumn::make('brand.name')
                    ->formatStateUsing(fn ($record) => $record->brand?->localized('name'))
                    ->searchable(),
                TextColumn::make('category.name')
                    ->formatStateUsing(fn ($record) => $record->category?->localized('name'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('gender')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('price')
                    ->money('AED')
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->money('AED')
                    ->sortable(),
                TextColumn::make('stock_quantity')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('featured_image_path'),
                IconColumn::make('is_featured')
                    ->boolean(),
                IconColumn::make('is_best_seller')
                    ->boolean(),
                IconColumn::make('is_new_arrival')
                    ->boolean(),
                IconColumn::make('is_uae_exclusive')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
                IconColumn::make('vat_taxable')
                    ->boolean(),
                TextColumn::make('weight_grams')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('average_rating')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reviews_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
