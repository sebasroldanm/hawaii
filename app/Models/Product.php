<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'price', 'is_in_stock', 'category', 'is_composite'];
    protected $casts = ['price' => 'float', 'is_in_stock' => 'boolean', 'is_composite' => 'boolean'];
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipes', 'composite_id', 'ingredient_id')
            ->withPivot('quantity');
    }
}
