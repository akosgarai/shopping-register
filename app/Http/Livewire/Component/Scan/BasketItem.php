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
    // quantity units options.
    public $quantityUnits;

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
        $item = (new Item())->firstOrCreate([
            'name' => $this->items[$index]['name'],
        ]);
        $this->items[$index]['itemId'] = $item->id;
        $this->getPredictions($dataPrediction);
    }

    public function deleteItem($index)
    {
        array_splice($this->items, $index, 1);
        // if the number of items is 0, update the total to 0.
        if (count($this->items) === 0) {
            $this->total = 0;
        }
        $this->recalculateTotal();
    }

    // It updates the total value and sends an update to the parent component.
    public function recalculateTotal($index = null)
    {

        // calculate the total from the items.
        if (count($this->items)) {
            if ($index !== null) {
                $this->items[$index]['unit_price'] = $this->items[$index]['price'] / $this->items[$index]['quantity'];
            }
            $this->total = array_sum(array_column($this->items, 'price'));
        }
        $this->emitUp('basket.data.update', ['items' => $this->items, 'total' => $this->total]);
    }

    public function addItem()
    {
        $this->items[] = [
            'name' => '',
            'price' => '',
            'itemId' => '',
            'quantity' => 1.00,
            'quantity_unit_id' => 3,
            'unit_price' => '',
            'suggestions' => [],
        ];
        $this->recalculateTotal();
    }

    public function finishedSetup()
    {
        $this->recalculateTotal();
        $this->emitUp('basket.data.done');
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
