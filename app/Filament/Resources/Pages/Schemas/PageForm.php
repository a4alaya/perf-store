<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title.en')
                    ->label('Title (English)')
                    ->required(),
                TextInput::make('title.ar')
                    ->label('Title (Arabic)'),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('body.en')
                    ->label('Body (English)')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('body.ar')
                    ->label('Body (Arabic)')
                    ->columnSpanFull(),
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
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
