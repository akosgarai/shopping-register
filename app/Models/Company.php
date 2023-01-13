<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Address;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tax_number',
        'address_id',
    ];

    /**
     * Get the address of this company.
     */
    public function address()
    {
        return $this->hasOne(Address::class);
    }

    /**
     * Get the shops of this company.
     */
    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}
