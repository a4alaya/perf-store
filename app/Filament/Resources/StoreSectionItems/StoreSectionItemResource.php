<?php

namespace App\Filament\Resources\StoreSectionItems;

use App\Filament\Resources\StoreSectionItems\Pages\CreateStoreSectionItem;
use App\Filament\Resources\StoreSectionItems\Pages\EditStoreSectionItem;
use App\Filament\Resources\StoreSectionItems\Pages\ListStoreSectionItems;
use App\Models\StoreSection;
use App\Models\StoreSectionItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class StoreSectionItemResource extends Resource
{
    protected static ?string $model = StoreSectionItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static ?string $navigationLabel = 'Section Items';

    protected static string|UnitEnum|null $navigationGroup = 'Storefront';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('store_section_id')
                    ->label('Store section')
                    ->options(fn () => StoreSection::query()->ordered()->get()->mapWithKeys(
                        fn (StoreSection $section) => [$section->id => $section->localized('title') ?: $section->key],
                    ))
                    ->searchable()
                    ->required(),
                TextInput::make('title.en')
                    ->label('Title (English)')
                    ->required(),
                TextInput::make('title.ar')
                    ->label('Title (Arabic)')
                    ->required(),
                Textarea::make('subtitle.en')
                    ->label('Subtitle (English)')
                    ->columnSpanFull(),
                Textarea::make('subtitle.ar')
                    ->label('Subtitle (Arabic)')
                    ->columnSpanFull(),
                Select::make('icon')
                    ->options([
                        'truck' => 'Truck',
                        'shield-check' => 'Shield check',
                        'receipt-percent' => 'Receipt percent',
                        'sparkles' => 'Sparkles',
                    ]),
                FileUpload::make('image_path')
                    ->disk('public')
                    ->directory('sections/items')
                    ->image(),
                TextInput::make('link_url')
                    ->label('Link URL'),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
                TextInput::make('sort_order')
                    ->numeric()
                    ->required()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('storeSection.key')
                    ->label('Section')
                    ->searchable(),
                TextColumn::make('title')
                    ->formatStateUsing(fn (StoreSectionItem $record) => $record->localized('title'))
                    ->searchable(),
                TextColumn::make('icon')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStoreSectionItems::route('/'),
            'create' => CreateStoreSectionItem::route('/create'),
            'edit' => EditStoreSectionItem::route('/{record}/edit'),
        ];
    }
}
