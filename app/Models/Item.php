<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the basket items of this item.
     */
    public function basketItems()
    {
        return $this->hasMany(BasketItem::class);
    }
}
