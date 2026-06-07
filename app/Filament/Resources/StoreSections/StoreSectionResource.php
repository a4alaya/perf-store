<?php

namespace App\Filament\Resources\StoreSections;

use App\Filament\Resources\StoreSections\Pages\CreateStoreSection;
use App\Filament\Resources\StoreSections\Pages\EditStoreSection;
use App\Filament\Resources\StoreSections\Pages\ListStoreSections;
use App\Models\StoreSection;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
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

class StoreSectionResource extends Resource
{
    protected static ?string $model = StoreSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $navigationLabel = 'Store Sections';

    protected static string|UnitEnum|null $navigationGroup = 'Storefront';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->helperText('Stable identifier, such as home.hero or home.featured.')
                    ->required(),
                Select::make('type')
                    ->options([
                        'hero' => 'Hero',
                        'feature_strip' => 'Feature strip',
                        'product_rail' => 'Product rail',
                        'split_product_feature' => 'Split product feature',
                        'taxonomy_grid' => 'Category / brand grid',
                        'review_grid' => 'Review grid',
                    ])
                    ->required(),
                TextInput::make('title.en')
                    ->label('Title (English)'),
                TextInput::make('title.ar')
                    ->label('Title (Arabic)'),
                Textarea::make('subtitle.en')
                    ->label('Subtitle (English)')
                    ->columnSpanFull(),
                Textarea::make('subtitle.ar')
                    ->label('Subtitle (Arabic)')
                    ->columnSpanFull(),
                TextInput::make('eyebrow.en')
                    ->label('Eyebrow (English)'),
                TextInput::make('eyebrow.ar')
                    ->label('Eyebrow (Arabic)'),
                Textarea::make('body.en')
                    ->label('Body (English)')
                    ->columnSpanFull(),
                Textarea::make('body.ar')
                    ->label('Body (Arabic)')
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->disk('public')
                    ->directory('sections')
                    ->image(),
                FileUpload::make('secondary_image_path')
                    ->disk('public')
                    ->directory('sections')
                    ->image(),
                TextInput::make('cta_label.en')
                    ->label('CTA label (English)'),
                TextInput::make('cta_label.ar')
                    ->label('CTA label (Arabic)'),
                TextInput::make('cta_url')
                    ->label('CTA URL'),
                TextInput::make('secondary_cta_label.en')
                    ->label('Secondary CTA label (English)'),
                TextInput::make('secondary_cta_label.ar')
                    ->label('Secondary CTA label (Arabic)'),
                TextInput::make('secondary_cta_url')
                    ->label('Secondary CTA URL'),
                Select::make('product_source')
                    ->options([
                        'featured' => 'Featured products',
                        'best_sellers' => 'Best sellers',
                        'new_arrivals' => 'New arrivals',
                        'uae_exclusive' => 'UAE exclusive',
                        'oud' => 'Oud',
                        'gift_sets' => 'Gift sets',
                    ]),
                Select::make('taxonomy_source')
                    ->options([
                        'categories' => 'Categories',
                        'brands' => 'Brands',
                    ]),
                Select::make('background_style')
                    ->options([
                        'default' => 'Default',
                        'light' => 'Light luxury',
                        'dark' => 'Dark luxury',
                        'soft' => 'Soft band',
                    ])
                    ->required()
                    ->default('default'),
                TextInput::make('limit')
                    ->numeric()
                    ->required()
                    ->default(8),
                KeyValue::make('settings')
                    ->columnSpanFull(),
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
                TextColumn::make('title')
                    ->label('Title')
                    ->formatStateUsing(fn (StoreSection $record) => $record->localized('title'))
                    ->searchable(),
                TextColumn::make('key')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('product_source')
                    ->searchable(),
                TextColumn::make('taxonomy_source')
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
            'index' => ListStoreSections::route('/'),
            'create' => CreateStoreSection::route('/create'),
            'edit' => EditStoreSection::route('/{record}/edit'),
        ];
    }
}
