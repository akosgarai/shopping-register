<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw',
    ];

    /**
     * Get the companies that are located on this address.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
