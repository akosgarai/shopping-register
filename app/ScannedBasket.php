<?php

namespace App;

class ScannedBasket
{
    public $id = '';
    public $taxNumber = '';
    public $marketAddress = '';
    public $marketName = '';
    public $companyAddress = '';
    public $companyName = '';
    public $date = '';
    public $total = '';
    public $items = [];

    public function toArray(): array
    {
        return [
            'id' => $this->id,
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
