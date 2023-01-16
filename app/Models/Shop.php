<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address_id',
        'company_id',
    ];

    /**
     * Get the address of this shop.
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    /**
     * Get the company of this shop.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the baskets of this shop.
     */
    public function baskets()
    {
        return $this->hasMany(Basket::class);
    }
}
