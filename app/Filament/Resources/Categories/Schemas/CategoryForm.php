<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->options(fn () => Category::query()->get()->mapWithKeys(fn (Category $category) => [$category->id => $category->localized('name')])),
                TextInput::make('name.en')
                    ->label('Name (English)')
                    ->required(),
                TextInput::make('name.ar')
                    ->label('Name (Arabic)'),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description.en')
                    ->label('Description (English)')
                    ->columnSpanFull(),
                Textarea::make('description.ar')
                    ->label('Description (Arabic)')
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->disk('public')
                    ->directory('categories')
                    ->image(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
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
            ]);
    }
}
