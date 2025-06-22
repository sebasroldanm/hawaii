<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use SoftDeletes;
    protected $fillable = ['title', 'seats', 'is_reserved', 'reservation_start', 'reservation_end'];
    protected $casts = [
        'is_reserved' => 'boolean',
        'reservation_start' => 'datetime',
        'reservation_end' => 'datetime',
    ];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
