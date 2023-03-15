<?php

namespace App\Http\Livewire\Component\Scan;

use Livewire\Component;

use App\Models\Address;
use App\Models\Shop as ShopModel;
use App\Services\DataPredictionService;

class Shop extends Component
{
    const DATA_TYPE_ADDRESS = 'address';
    const DATA_TYPE_SHOP = 'shop';

    // The scanned texts.
    public $scannedName = '';
    public $scannedAddress = '';

    // Edited values.
    public $name = '';
    public $address = '';

    // This is the value set by the Company component.
    public $shopCompany = '';

    // Selections
    public $selectedShop = '';
    public $selectedAddress = '';

    public $addressSuggestions = [];
    public $shopSuggestions = [];

    // Save new options.
    public $allowSaveAddress = false;
    public $allowSaveShop = false;

    protected $listeners = [
        'basket.data.extracted' => 'basketDataHandler',
        'shop.data.select' => 'shopDataHandler',
    ];

    public function basketDataHandler($basket, DataPredictionService $dataPrediction)
    {
        $this->scannedName = $basket['marketName'];
        $this->scannedAddress = $basket['marketAddress'];
        $this->shopCompany = $basket['companyId'] ?? '';
        $this->mount();
        $this->getPredictions($dataPrediction);
    }

    public function mount()
    {
        $this->name = $this->scannedName;
        $this->address = $this->scannedAddress;
    }

    public function render(DataPredictionService $dataPrediction)
    {
        $this->getPredictions($dataPrediction);
        return view('livewire.component.scan.shop');
    }

    public function insertNew($data, DataPredictionService $dataPrediction)
    {
        switch ($data) {
        case self::DATA_TYPE_ADDRESS:
            $this->addAddress($dataPrediction);
            break;
        case self::DATA_TYPE_SHOP:
            $this->addShop($dataPrediction);
            break;
        }
    }

    public function shopDataHandler($data, DataPredictionService $dataPrediction)
    {
        switch ($data) {
        case self::DATA_TYPE_ADDRESS:
            $this->updateAddress($dataPrediction);
            break;
        case self::DATA_TYPE_SHOP:
            $this->updateShop($dataPrediction);
            break;
        }
    }

    public function validateInputs()
    {
        $this->validate([
            'name' => 'required|string',
        ]);
        $this->emitUp('basket.data.update', ['marketId' => $this->selectedShop]);
    }

    private function getPredictions(DataPredictionService $dataPrediction)
    {
        $this->addressSuggestions = $dataPrediction->getAddressSuggestions($this->address, 10)->toArray();
        $this->shopSuggestions = $dataPrediction->getShopSuggestions($this->shopCompany, $this->name, 10)->toArray();
        // setup the selected company and address if the distance is small enough
        $firstAddress = $this->addressSuggestions[0] ?? null;
        $firstShop = $this->shopSuggestions[0] ?? null;
        $this->allowSaveAddress = is_null($firstAddress);
        $this->allowSaveShop = false;
        if ($firstAddress) {
            if ($firstAddress['distance'] > 0) {
                $this->allowSaveAddress = true;
            }
            // if the address distance is 0, then we can set the selected address
            if ($firstAddress['distance'] == 0) {
                $this->selectedAddress = $firstAddress['raw'];
            }
            if (!$this->allowSaveAddress) {
                $this->allowSaveShop = is_null($firstShop);
                if ($firstShop && $firstShop['distance'] > 0) {
                    $this->allowSaveShop = true;
                }
            }
        }
    }

    private function addAddress(DataPredictionService $dataPrediction)
    {
        $this->validate([
            'address' => 'required|string',
        ]);
        (new Address())->firstOrCreate([
            'raw' => $this->address,
        ]);
        $this->getPredictions($dataPrediction);
    }

    private function addShop(DataPredictionService $dataPrediction)
    {
        $this->validate([
            'name' => 'required|string',
        ]);
        (new ShopModel())->firstOrCreate([
            'name' => $this->name,
            'company_id' => $this->shopCompany,
            'address_id' => $this->addressSuggestions[0]['id'],
        ]);
        $this->getPredictions($dataPrediction);
    }

    private function updateAddress(DataPredictionService $dataPrediction)
    {
        $this->address = $this->selectedAddress;
        $this->getPredictions($dataPrediction);
    }

    private function updateShop(DataPredictionService $dataPrediction)
    {
        if ($this->selectedShop != '') {
            $choosenShop = ShopModel::where('id', $this->selectedShop)->where('company_id', $this->shopCompany)->first();
            if ($choosenShop) {
                $this->name = $choosenShop->name;
                $this->address = $choosenShop->address->raw;
            }
        }
        $this->getPredictions($dataPrediction);
    }
}
