<?php

namespace App\Http\Livewire\Component\Scan;

use Livewire\Component;

use App\Models\Item;
use App\Services\DataPredictionService;

class BasketItem extends Component
{
    // The scanned texts.
    public $scannedTotal = '';
    public $scannedItems = [];

    // The edited values.
    public $total = '';
    public $items = [];

    // This is the value set by the Shop component.
    public $shopId = '';

    protected $listeners = [
        'basket.data.extracted' => 'basketDataHandler',
    ];

    public function mount()
    {
        $this->total = $this->scannedTotal;
        $this->items = $this->scannedItems;
        // extend the items with the itemId key if not already set.
        foreach ($this->items as $key => $item) {
            if (!isset($item['itemId'])) {
                $this->items[$key]['itemId'] = '';
            }
        }
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

    public function insertNew($index, DataPredictionService $dataPrediction)
    {
        $item = Item::firstOrCreate([
            'name' => $this->items[$index]['name'],
        ]);
        $this->items[$index]['itemId'] = $item->id;
        $this->emitUp('basket.data.update', ['items' => $this->items, 'total' => $this->total]);
        $this->getPredictions($dataPrediction);
    }

    public function deleteItem($index)
    {
        array_splice($this->items, $index, 1);
        $this->emitUp('basket.data.update', ['items' => $this->items, 'total' => $this->total]);
    }

    public function addItem()
    {
        $this->items[] = [
            'name' => '',
            'price' => '',
            'itemId' => '',
            'suggestions' => [],
        ];
        $this->emitUp('basket.data.update', ['items' => $this->items, 'total' => $this->total]);
    }

    private function getPredictions(DataPredictionService $dataPrediction)
    {
        foreach ($this->items as $key => $item) {
            if ($item['name'] == '') {
                $this->items[$key]['suggestions'] = Item::selectRaw("items.*, '' as distance, '' as percentage")->get();
                continue;
            }
            $this->items[$key]['suggestions'] = $dataPrediction->getItemSuggestions($item['name']);
            // if the current selection is not set and the first suggestion has 0 distance, set it.
            if ($item['itemId'] == '' && count($this->items[$key]['suggestions']) > 0 && $this->items[$key]['suggestions'][0]['distance'] == 0) {
                $this->items[$key]['itemId'] = $this->items[$key]['suggestions'][0]['id'];
            }
        }
    }
}
