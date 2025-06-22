<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTableHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'from_table_id', 'to_table_id', 'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function fromTable()
    {
        return $this->belongsTo(Table::class, 'from_table_id');
    }

    public function toTable()
    {
        return $this->belongsTo(Table::class, 'to_table_id');
    }
}
