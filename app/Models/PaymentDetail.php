<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payment_id', 'order_detail_id', 'payment_method_id', 'amount',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
