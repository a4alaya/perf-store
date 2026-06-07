<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title.en')
                    ->label('Title (English)')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('title.ar')
                    ->label('Title (Arabic)')
                    ->columnSpanFull(),
                Textarea::make('subtitle.en')
                    ->label('Subtitle (English)')
                    ->columnSpanFull(),
                Textarea::make('subtitle.ar')
                    ->label('Subtitle (Arabic)')
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->disk('public')
                    ->directory('banners')
                    ->image(),
                TextInput::make('link_url')
                    ->url(),
                TextInput::make('location')
                    ->required()
                    ->default('home'),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('ends_at'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
