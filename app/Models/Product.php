<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'team_id',
        'name',
        'price',
        'cost',
        'description',
        'image',
        'status',
        'product_category_id',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
