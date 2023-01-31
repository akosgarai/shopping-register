<?php

namespace App\Services;

use App\ScannedBasket;

abstract class AbstractParserService
{
    abstract public function parse(string $rawText): ScannedBasket;
}
