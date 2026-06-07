<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                FileUpload::make('logo_path')
                    ->disk('public')
                    ->directory('brands')
                    ->image(),
                TextInput::make('country'),
                Toggle::make('is_active')
                    ->required(),
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
