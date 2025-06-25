<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'table_id',
        'name',
        'status',
        'is_paid',
        'started_at',
        'completed_at',
    ];

    protected $attributes = [
        'status' => 'pending',
        'is_paid' => false,
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function history()
    {
        return $this->hasMany(OrderTableHistory::class);
    }
}
