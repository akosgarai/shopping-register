<?php

namespace App;

use App\Models\Company;
use App\Models\Item;
use App\Models\Shop;

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
        $data = [
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
        if ($this->companyId) {
            $data['companyId'] = $this->companyId;
            $company = Company::where('id', $this->companyId)->with('address')->first();
            $data['companyName'] = $company->name;
            $data['companyAddress'] = $company->address->raw;
            $data['taxNumber'] = $company->tax_number;
        }
        if ($this->marketId) {
            $data['marketId'] = $this->marketId;
            $shop = Shop::where('id', $this->marketId)->with('address')->first();
            $data['marketName'] = $shop->name;
            $data['marketAddress'] = $shop->address->raw;
        }
        foreach ($data['items'] as $key => $item) {
            if (isset($item['itemId']) && $item['itemId'] !== '') {
                $storedItem = Item::where('id', $item['itemId'])->first();
                $data['items'][$key]['name'] = $storedItem->name;
            }
        }
        return $data;
    }

    public static function fromArray(array $data): ScannedBasket
    {
        $basket = new ScannedBasket();
        $basket->basketId = $data['basketId'];
        $basket->taxNumber = $data['taxNumber'];
        $basket->marketAddress = $data['marketAddress'];
        $basket->marketName = $data['marketName'];
        $basket->companyAddress = $data['companyAddress'];
        $basket->companyName = $data['companyName'];
        $basket->date = $data['date'];
        $basket->total = $data['total'];
        $basket->items = $data['items'];
        if (isset($data['companyId'])) {
            $basket->companyId = $data['companyId'];
        }
        if (isset($data['marketId'])) {
            $basket->marketId = $data['marketId'];
        }
        return $basket;
    }
}
