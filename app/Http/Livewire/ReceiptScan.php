<?php

namespace App\Http\Livewire;

use Alimranahmed\LaraOCR\Services\OcrAbstract;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

use App\Models\Basket;
use App\ScannedBasket;
use App\Services\BasketExtractorService;
use App\Services\DataPredictionService;
use App\Services\ImageService;

class ReceiptScan extends Component
{
    const ACTION_PICK = 'pick';
    const ACTION_EDIT = 'edit';
    const ACTION_PARSE = 'parse';
    const ACTION_BASKET = 'basket';

    const BASKET_TAB_ID = 'basket-id';
    const BASKET_TAB_SIMILAR = 'basket-same';

    const PANEL_PICK_IMAGE = 'pickImagePanel';
    const PANEL_BASKET_ID = 'basketIDPanel';

    // query parameters
    public $action = '';
    public $imagePath = '';
    public $parserApplication = '';

    public $rawExtractedText = '';
    public $basket = [];

    public $basketPreview = null;

    public $createBasketTab = '';

    protected $listeners = [
        'panel.close' => 'closePanel',
        'basket.image' => 'basketImageHandler',
        'temp.image' => 'tempImageHandler',
        'image.editing' => 'imageEditingHandler',
        'basket.data' => 'basketDataHandler',
        'basket.data.update' => 'basketDataUpdateHandler',
    ];

    // The query string parameters.
    protected $queryString = [
        'action' => ['except' => ''],
        'imagePath' => ['except' => '', 'as' => 'document'],
        'parserApplication' => ['except' => '', 'as' => 'parser'],
    ];

    // Initialize the component based on the query string parameters.
    public function mount(ImageService $imageService, BasketExtractorService $basketExtractor, DataPredictionService $dataPrediction)
    {
        $this->action = request()->query('action', '');
        if ($this->action == self::ACTION_PARSE || $this->action == self::ACTION_BASKET) {
            $this->extractText($imageService);

            if ($this->action == self::ACTION_BASKET) {
                $this->parseText($this->parserApplication, $basketExtractor, $dataPrediction);
            }
        }
    }

    public function render()
    {
        return view('livewire.receipt-scan')->extends('layouts.app');
    }

    public function tempImageHandler($action, $imageName = null)
    {
        switch ($action) {
            case 'load':
                $this->emitSelf("panel.close", self::PANEL_PICK_IMAGE);
                $this->loadTempImage($imageName);
                break;
            case 'openpanel':
                // close the basket preview panel if it is open
                $this->emitTo('component.panel', 'panel.close', self::PANEL_BASKET_ID);
                $this->action = self::ACTION_PICK;
                $this->parserApplication = '';
                $this->imagePath = '';
                $this->emit("panel.open", self::PANEL_PICK_IMAGE);
                break;
        }
    }
    public function basketImageHandler($action, $targetBasketId, ImageService $imageService)
    {
        switch ($action) {
        case 'add':
            $this->addImageToBasket($targetBasketId, $imageService);
            break;
        case 'change':
            $this->changeBasketImage($targetBasketId, $imageService);
            break;
        }
    }

    public function imageEditingHandler($action, $imageData = '', ImageService $imageService)
    {
        switch ($action) {
            case 'finished':
                $this->saveEditedImage($imageData, $imageService);
                break;
            case 'start':
                $this->editStep();
                break;
        }
    }

    public function basketDataHandler($dataName)
    {
        switch ($dataName) {
            case 'basketId':
                $this->createBasketTab = self::BASKET_TAB_ID;
                $this->emit('panel.update', self::PANEL_BASKET_ID, [ 'basket' => $this->basket ]);
                $this->emit("panel.open", self::PANEL_BASKET_ID);
                break;
        }
    }

    public function basketDataUpdateHandler($dataName, $newValue)
    {
        switch ($dataName) {
            case 'basketId':
                $this->basket['id'] = $newValue;
                break;
        }
    }
    public function selectParserClickHandler(ImageService $imageService)
    {
        // close the basket preview panel if it is open
        $this->emitTo('component.panel', 'panel.close', self::PANEL_BASKET_ID);
        $this->parserApplication = '';
        $this->extractText($imageService);
        $this->action = self::ACTION_PARSE;
    }

    // Event handler for the editor save event.
    private function saveEditedImage($imageData, ImageService $imageService)
    {
        $imageService->updateTempImageOfUser($this->imagePath, auth()->user()->id, $imageData);
        $this->selectParserClickHandler($imageService);
    }

    // It parses the extracted text with the selected parser application.
    public function parseText($parserApplication, BasketExtractorService $basketExtractor)
    {
        $this->parserApplication = $parserApplication;
        $basket = $basketExtractor->parseTextWith($this->rawExtractedText, $this->parserApplication);
        $this->basket = $basket->toArray();
        $this->action = self::ACTION_BASKET;

        $this->emit('basket.data.extracted', $this->basket);
        $this->basketDataHandler('basketId');
    }

    public function closePanel($panelName)
    {
        switch ($panelName) {
        case self::PANEL_BASKET_ID:
                $this->emitTo('component.panel', 'panel.close', self::PANEL_BASKET_ID);
                break;
            case self::PANEL_PICK_IMAGE:
                $this->emitTo('component.panel', 'panel.close', self::PANEL_PICK_IMAGE);
                if ($this->action == self::ACTION_PICK) {
                    $this->action = '';
                }
                break;
        }
    }

    private function extractText(ImageService $imageService)
    {
        $ocr = app()->make(OcrAbstract::class);
        $this->rawExtractedText = $ocr->scan($imageService->tempFilePath($this->imagePath, auth()->user()->id));
    }

    // It sets the imagePath parameter to the value passed in and loads the image to the editor.
    private function loadTempImage($imageName)
    {
        $this->imagePath = $imageName;
        $this->editStep();
    }

    private function addImageToBasket($targetBasketId, ImageService $imageService)
    {
        // move the image from the temp folder to the receipt folder
        $imageService->moveReceiptImageFromTempToReceiptUserFolder($this->imagePath, auth()->user()->id);
        // and add the image to the basket
        $basket = Basket::where('id', $targetBasketId)
            ->where('user_id', auth()->user()->id)
            ->first();
        if ($basket) {
            $basket->receipt_url = $this->imagePath;
            $basket->save();
        }
        return redirect()->route('basket', ['id' => $targetBasketId]);
    }
    private function changeBasketImage($targetBasketId, ImageService $imageService)
    {
        $userId = auth()->user()->id;
        // delete the current image
        $basket = Basket::where('id', $targetBasketId)
            ->where('user_id', $userId)
            ->first();
        if ($basket) {
            $imageService->deleteReceiptImageFromUserFolder($basket->receipt_url, $userId);
        }
        return $this->addImageToBasket($targetBasketId, $imageService);
    }

    // Action and frontend initialization for the edit step.
    // It sets the action parameter to ACTION_EDIT and dispatches the 'receiptScan.edit' browser event
    // to initialize the frontend, loads the image to the editor.
    private function editStep()
    {
        $this->action = self::ACTION_EDIT;
        $this->emitTo('component.panel', 'panel.close', self::PANEL_BASKET_ID);
        $this->parserApplication = '';
        $this->dispatchBrowserEvent('receiptScan.edit', ['imagePath' => route('image.viewTemp', ['filename' => $this->imagePath, 'v' => time()])]);
    }
}
