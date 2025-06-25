<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderDetail;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function handleRecordCreation(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Extraer orderDetails del array general
            $orderDetailsData = $data['orderDetails'] ?? [];
            unset($data['orderDetails']);

            // Crear la orden
            $order = Order::create([
                'table_id'    => $data['table_id'],
                'name'        => $data['name'] ?? null,
                'status'      => 'pending',
                'is_paid'     => false,
                'started_at'  => now(),
            ]);

            // Crear los detalles de la orden
            foreach ($orderDetailsData as $item) {
                OrderDetail::create([
                    'order_id'    => $order->id,
                    'product_id'  => $item['product_id'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'state'       => 'queued',
                ]);
            }

            return $order;
        });
    }
}
