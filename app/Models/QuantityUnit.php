<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuantityUnit extends Model
{
    const UNIT_KG = 'kg';
    const UNIT_G = 'g';
    const UNIT_PCS = 'pcs';

    const UNITS = [
        self::UNIT_KG,
        self::UNIT_G,
        self::UNIT_PCS,
    ];

    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
