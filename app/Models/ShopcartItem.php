<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopcartItem extends Model
{
    protected $fillable = [
        'shopcart_id',
        'product_id',
        'unit_price',
        'is_paid',
        'note',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
    ];

    public function shopcart(): BelongsTo
    {
        return $this->belongsTo(Shopcart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
