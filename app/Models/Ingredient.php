<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'stock', 'unit', 'low_stock_threshold'];
    protected $casts = ['stock' => 'float', 'low_stock_threshold' => 'float'];
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}
