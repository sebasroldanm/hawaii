<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'is_in_stock',
        'is_composite',
        'stock',
        'preparation_time',
        'preparation_area',
        'category_id',
    ];

    protected $casts = [
        'price' => 'float',
        'is_in_stock' => 'boolean',
        'is_composite' => 'boolean',
        'stock' => 'float',
        'preparation_time' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'composite_id');
    }
}
