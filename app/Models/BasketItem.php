<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasketItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'basket_id',
        'item_id',
        'price',
    ];

    /**
     * Get the basket of this item.
     */
    public function basket()
    {
        return $this->hasOne(Basket::class);
    }

    /**
     * Get the item of this basket item.
     */
    public function item()
    {
        return $this->hasOne(Item::class);
    }
}
