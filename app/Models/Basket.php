<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_id',
        'date',
        'total',
        'receipt_id',
        'receipt_url',
    ];

    /**
     * Get the user that bought this basket.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the shop that sold this basket.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the basket items of this basket.
     */
    public function basketItems()
    {
        return $this->hasMany(BasketItem::class);
    }
}
