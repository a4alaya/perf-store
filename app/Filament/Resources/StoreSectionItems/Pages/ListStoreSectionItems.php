<?php

namespace App\Filament\Resources\StoreSectionItems\Pages;

use App\Filament\Resources\StoreSectionItems\StoreSectionItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStoreSectionItems extends ListRecords
{
    protected static string $resource = StoreSectionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
