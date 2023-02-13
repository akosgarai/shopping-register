<?php

namespace App\Http\Livewire\Component\Scan;

use Livewire\Component;

use App\Services\DataPredictionService;

class BasketItem extends Component
{
    // The scanned texts.
    public $scannedTotal = '';
    public $scannedItems = [];

    // The edited values.
    public $total = '';
    public $items = '';

    // This is the value set by the Shop component.
    public $shopId = '';

    protected $listeners = [
        'basket.data.extracted' => 'basketDataHandler',
    ];

    public function mount()
    {
        $this->total = $this->scannedTotal;
        $this->items = $this->scannedItems;
    }

    public function render()
    {
        return view('livewire.component.scan.basket-item');
    }

    public function basketDataHandler($basket, DataPredictionService $dataPrediction)
    {
        $this->scannedTotal = $basket['total'];
        $this->scannedItems = $basket['items'];
        $this->shopId = $basket['marketId'] ?? null;
        $this->mount();
        $this->getPredictions($dataPrediction);
    }
    private function getPredictions(DataPredictionService $dataPrediction)
    {
    }
}
