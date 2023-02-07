<?php

namespace App\Http\Livewire\Component\Scan;

use Livewire\Component;

use App\Services\DataPredictionService;

/*
 * This LiveWire component is used to setup the receipt id during the scan process.
 * It shows the scanned receipt id and allows the user to change it.
 * There are suggestions for existing receipt ids (baskets).
 * The user can select one of the suggestions and allowed to add or change the receipt image.
 * If existing baskets are not selected, a new basket will be created with the current receipt id.
 * */
class Basket extends Component
{
    // The scanned text that might be a receipt id.
    public $scannedBasketId = '';
    // The receipt id that is editable by the user.
    public $basketId = '';
    // Basket suggestions based on the basketId
    public $suggestions = [];
    // Basket choosen from the suggestions that we display.
    public $basketPreview = null;

    protected $listeners = [
        'basket.data.extracted' => 'basketDataHandler',
    ];

    public function mount(DataPredictionService $dataPrediction)
    {
        $this->basketId = $this->scannedBasketId;
        $this->getBasketPredictions($dataPrediction);
    }

    public function basketDataHandler($basket, DataPredictionService $dataPrediction)
    {
        $this->scannedBasketId = $basket['id'];
        $this->basketId = $basket['id'];
        $this->getBasketPredictions($dataPrediction);
    }

    public function render(DataPredictionService $dataPrediction)
    {
        $this->getBasketPredictions($dataPrediction);
        return view('livewire.component.scan.basket');
    }

    public function selectPreview($index)
    {
        $this->basketPreview = $this->suggestions[$index];
    }

    public function validateInputs()
    {
        $this->validate([
            'basketId' => 'required|string',
        ]);
        $this->emitUp('basket.data.update', 'basketId', $this->basketId);
    }

    /*
     * It is called to get the suggestions for the basketId.
     * */
    private function getBasketPredictions(DataPredictionService $dataPrediction)
    {
        $this->suggestions = $dataPrediction->getBasketSuggestions(auth()->user()->id, $this->basketId, 10)->toArray();
    }
}
