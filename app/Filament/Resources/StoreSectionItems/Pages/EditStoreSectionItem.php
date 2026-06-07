<?php

namespace App\Filament\Resources\StoreSectionItems\Pages;

use App\Filament\Resources\StoreSectionItems\StoreSectionItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStoreSectionItem extends EditRecord
{
    protected static string $resource = StoreSectionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
