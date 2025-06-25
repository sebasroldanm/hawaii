<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'composite_id',
        'ingredient_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'composite_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
