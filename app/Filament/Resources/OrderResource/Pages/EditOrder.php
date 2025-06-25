<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderDetail;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate($record, array $data): Order
    {
        return DB::transaction(function () use ($record, $data) {
            $orderDetailsData = $data['orderDetails'] ?? [];
            unset($data['orderDetails']);

            // Actualizar la orden
            $record->update([
                'table_id' => $data['table_id'],
                'name'     => $data['name'] ?? null,
            ]);

            // Crear o actualizar detalles
            foreach ($orderDetailsData as $item) {
                if (!empty($item['id'])) {
                    // Update existente
                    $detail = OrderDetail::find($item['id']);
                    if ($detail) {
                        $detail->update([
                            'product_id' => $item['product_id'],
                            'quantity'   => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                        ]);
                    }
                } else {
                    // Nuevo detalle
                    OrderDetail::create([
                        'order_id'   => $record->id,
                        'product_id' => $item['product_id'],
                        'quantity'   => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'state'      => 'queued',
                    ]);
                }
            }

            return $record;
        });
    }
}
