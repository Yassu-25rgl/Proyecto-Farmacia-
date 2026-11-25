<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'category_id',
    'name',
    'amount',
    'price_unit',
    'subtotal',
    'image',
])]
class Medicine extends Model
{
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}