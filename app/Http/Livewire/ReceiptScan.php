<?php

namespace App\Http\Livewire;

use Alimranahmed\LaraOCR\Services\OcrAbstract;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

use App\Models\Basket;
use App\Models\Company;
use App\Models\Shop;
use App\ScannedBasket;
use App\Services\BasketExtractorService;
use App\Services\ImageService;

class ReceiptScan extends Component
{
    const ACTION_PICK = 'pick';
    const ACTION_EDIT = 'edit';
    const ACTION_PARSE = 'parse';
    const ACTION_BASKET = 'basket';

    const BASKET_TAB_ID = 'basket-id';
    const BASKET_TAB_COMPANY = 'basket-company';
    const BASKET_TAB_SHOP = 'basket-shop';

    const PANEL_PICK_IMAGE = 'pickImagePanel';
    const PANEL_BASKET_ID = 'basketIDPanel';
    const PANEL_BASKET_COMPANY = 'basketCompanyPanel';
    const PANEL_BASKET_SHOP = 'basketShopPanel';

    const PANELS = [
        self::PANEL_PICK_IMAGE,
        self::PANEL_BASKET_ID,
        self::PANEL_BASKET_COMPANY,
        self::PANEL_BASKET_SHOP,
    ];

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
        'createBasketTab' => ['except' => '', 'as' => 'tab'],
    ];

    // Initialize the component based on the query string parameters.
    public function mount(ImageService $imageService, BasketExtractorService $basketExtractor)
    {
        $this->action = request()->query('action', '');
        if ($this->action == self::ACTION_PARSE || $this->action == self::ACTION_BASKET) {
            $this->extractText($imageService);

            if ($this->action == self::ACTION_BASKET) {
                $this->parseText($this->parserApplication, $basketExtractor);
            }
        }
    }

    public function render()
    {
        return view('livewire.receipt-scan')->extends('layouts.app');
    }

    public function tempImageHandler($action, $imageName = null)
    {
        $this->closePanelsExcept('');
        switch ($action) {
            case 'load':
                $this->loadTempImage($imageName);
                break;
            case 'openpanel':
                // close the basket preview panel if it is open
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
                $this->closePanelsExcept(self::PANEL_BASKET_ID);
                $this->createBasketTab = self::BASKET_TAB_ID;
                $this->emit('panel.update', self::PANEL_BASKET_ID, [ 'basket' => $this->basket ]);
                $this->emit("panel.open", self::PANEL_BASKET_ID);
                break;
            case 'companyId':
                $this->closePanelsExcept(self::PANEL_BASKET_COMPANY);
                $this->createBasketTab = self::BASKET_TAB_COMPANY;
                $this->emit('panel.update', self::PANEL_BASKET_COMPANY, [ 'basket' => $this->basket ]);
                $this->emit("panel.open", self::PANEL_BASKET_COMPANY);
                break;
            case 'shopId':
                $this->closePanelsExcept(self::PANEL_BASKET_SHOP);
                $this->createBasketTab = self::BASKET_TAB_SHOP;
                $this->emit('panel.update', self::PANEL_BASKET_SHOP, [ 'basket' => $this->basket ]);
                $this->emit("panel.open", self::PANEL_BASKET_SHOP);
                break;
        }
    }

    public function basketDataUpdateHandler($dataName, $newValue)
    {
        switch ($dataName) {
            case 'basketId':
                $this->basket['id'] = $newValue;
                break;
            case 'companyId':
                $this->basket['company_id'] = $newValue;
                $company = Company::where('id', $newValue)->with('address')->first();
                $this->basket['companyName'] = $company->name;
                $this->basket['companyAddress'] = $company->address->raw;
                $this->basket['taxNumber'] = $company->tax_number;
                break;
            case 'shopId':
                $this->basket['shop_id'] = $newValue;
                $shop = Shop::where('id', $newValue)->with('address')->first();
                $this->basket['marketName'] = $shop->name;
                $this->basket['marketAddress'] = $shop->address->raw;
                break;
        }
        // Notify the components about the changes.
        $this->emit('basket.data.extracted', $this->basket);
    }
    public function selectParserClickHandler(ImageService $imageService)
    {
        // close the basket preview panel if it is open
        $this->closePanelsExcept('');
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
        $this->emitTo('component.panel', 'panel.close', $panelName);
        switch ($panelName) {
        case self::PANEL_BASKET_ID:
        case self::PANEL_BASKET_COMPANY:
                $this->createBasketTab = '';
                break;
            case self::PANEL_PICK_IMAGE:
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
        $this->closePanelsExcept('');
        $this->parserApplication = '';
        $this->dispatchBrowserEvent('receiptScan.edit', ['imagePath' => route('image.viewTemp', ['filename' => $this->imagePath, 'v' => time()])]);
    }

    // Close every panel except the one with the given name.
    private function closePanelsExcept($panelName)
    {
        foreach (self::PANELS as $panel) {
            if ($panel != $panelName) {
                $this->emitTo('component.panel', 'panel.close', $panel);
            }
        }
    }
}
