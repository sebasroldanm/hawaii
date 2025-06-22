<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    protected $fillable = ['order_id', 'payment_method', 'amount', 'receipt'];
    protected $casts = ['amount' => 'float'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function details()
    {
        return $this->hasMany(PaymentDetail::class);
    }
}
