<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shopcart extends Model
{
    protected $fillable = [
        'table_id',
        'status',
        'total_amount',
        'paid_amount',
        'created_by',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
