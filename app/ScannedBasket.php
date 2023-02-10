<?php

namespace App;

class ScannedBasket
{
    public $basketId = '';
    public $taxNumber = '';
    public $marketAddress = '';
    public $marketName = '';
    public $companyAddress = '';
    public $companyName = '';
    public $date = '';
    public $total = '';
    public $items = [];

    // The following parameters are added during the manual data correction
    public $companyId = '';
    public $marketId = '';

    public function toArray(): array
    {
        return [
            'basketId' => $this->basketId,
            'taxNumber' => $this->taxNumber,
            'marketAddress' => $this->marketAddress,
            'marketName' => $this->marketName,
            'companyAddress' => $this->companyAddress,
            'companyName' => $this->companyName,
            'date' => $this->date,
            'total' => $this->total,
            'items' => $this->items,
        ];
    }
}
