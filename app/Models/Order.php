<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $fillable = ['table_id', 'name', 'is_paid'];
    protected $casts = ['is_paid' => 'boolean'];
    public function table()
    {
        return $this->belongsTo(Table::class);
    }
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
