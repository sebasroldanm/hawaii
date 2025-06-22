<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'unit_price',
        'estimated_delivery_time', 'state',
        'started_at', 'completed_at',
        'note', 'customer_label',
    ];

    protected $casts = [
        'estimated_delivery_time' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function paymentDetails()
    {
        return $this->hasMany(PaymentDetail::class);
    }
}
