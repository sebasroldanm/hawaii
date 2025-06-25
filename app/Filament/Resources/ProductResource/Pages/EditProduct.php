<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
{
    // Si el producto ya no es compuesto, eliminar recetas del modelo
    if (
        isset($data['is_composite']) &&
        $data['is_composite'] === false
    ) {
        $this->record->recipes()->delete();
        $data['recipes'] = []; // Limpiar datos antes de guardar
    }

    return $data;
}

}
