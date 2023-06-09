<?php

namespace App\Http\Livewire;

use Illuminate\Validation\ValidationException;
use Livewire\Component;
use thiagoalessio\TesseractOCR\TesseractOCR;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\QuantityUnit;
use App\ScannedBasket;
use App\Services\BasketExtractorService;
use App\Services\ImageService;

class ReceiptScan extends Component
{
    // Image picker panel has to be shown for picking an image
    const ACTION_PICK = 'pick';
    // The editor has to be shown for cropping the image
    const ACTION_EDIT = 'edit';
    // The parser selectors has to be shown for selecting the parser
    const ACTION_PARSE = 'parse';
    // The receipt id (basket id) panel has to be shown for editing the receipt id.
    const ACTION_BASKET = 'basket';
    // The company panel has to be shown for editing the company.
    const ACTION_COMPANY = 'company';
    // The shop panel has to be shown for editing the shop.
    const ACTION_SHOP = 'shop';
    // The basket item panel has to be shown for editing the basket items.
    const ACTION_BASKET_ITEMS = 'basket-items';

    const ACTION_STEP = [
        self::ACTION_PICK,
        self::ACTION_EDIT,
        self::ACTION_PARSE,
        self::ACTION_BASKET,
        self::ACTION_COMPANY,
        self::ACTION_SHOP,
        self::ACTION_BASKET_ITEMS,
    ];

    const PANEL_PICK_IMAGE = 'pickImagePanel';
    const PANEL_PARSER = 'parsersPanel';
    const PANEL_BASKET_ID = 'basketIDPanel';
    const PANEL_BASKET_COMPANY = 'basketCompanyPanel';
    const PANEL_BASKET_SHOP = 'basketShopPanel';
    const PANEL_BASKET_ITEMS = 'basketItemsPanel';

    const PANELS_FOR_ACTION = [
        self::ACTION_PICK => self::PANEL_PICK_IMAGE,
        self::ACTION_BASKET => self::PANEL_BASKET_ID,
        self::ACTION_COMPANY => self::PANEL_BASKET_COMPANY,
        self::ACTION_SHOP => self::PANEL_BASKET_SHOP,
        self::ACTION_PARSE => self::PANEL_PARSER,
        self::ACTION_BASKET_ITEMS => self::PANEL_BASKET_ITEMS,
    ];

    // query parameters
    public $action = '';
    public $imagePath = '';
    public $parserApplication = '';

    public $rawExtractedText = '';
    public $basket = [];

    protected $listeners = [
        // navigation and extraction steps
        'action.change' => 'actionChangeHandler',
        'action.next' => 'actionNextHandler',
        'action.back' => 'actionBackHandler',

        // add or update the choosen image to an existing basket
        'basket.image' => 'basketImageHandler',
        'temp.image.load' => 'tempImageHandler',
        'image.editing' => 'imageEditingHandler',
        'basket.data.update' => 'basketDataUpdateHandler',
        'basket.data.done' => 'basketDataFinishedHandler',
        'parse.text.with' => 'parseTextHandler',
    ];

    // The query string parameters.
    protected $queryString = [
        'action' => ['except' => ''],
        'imagePath' => ['except' => '', 'as' => 'document'],
        'parserApplication' => ['except' => '', 'as' => 'parser'],
    ];

    // Initialize the component based on the query string parameters.
    public function mount(ImageService $imageService, BasketExtractorService $basketExtractor)
    {
        $this->action = request()->query('action', '');
        $step = array_search($this->action, self::ACTION_STEP);
        if ($step && $step >= array_search(self::ACTION_BASKET, self::ACTION_STEP)) {
            $this->extractText($imageService, $basketExtractor);
            $this->parseText($basketExtractor);
        }
        if ($this->action != '') {
            $this->activateAction($this->action, $basketExtractor);
        }
    }

    public function render(BasketExtractorService $basketExtractor)
    {
        return view('livewire.receipt-scan', ['quantityUnits' => (new QuantityUnit())->all(), 'parserApplications' => $basketExtractor->getParserApplications()])->extends('layouts.app');
    }

    // action next closes the current action and moves to the next action
    public function actionNextHandler(BasketExtractorService $basketExtractor)
    {
        $currentActionIndex = array_search($this->action, self::ACTION_STEP);
        $nextActionIndex = min($currentActionIndex + 1, count(self::ACTION_STEP) - 1);
        $this->actionChangeHandler(self::ACTION_STEP[$nextActionIndex], $basketExtractor);
    }
    // action next closes the current action and moves to the next action
    public function actionBackHandler(BasketExtractorService $basketExtractor)
    {
        $currentActionIndex = array_search($this->action, self::ACTION_STEP);
        if ($currentActionIndex == 0) {
            $this->closeCurrentAction();
            $this->action = '';
            return;
        }
        $this->actionChangeHandler(self::ACTION_STEP[$currentActionIndex - 1], $basketExtractor);
    }

    // action change handler is for the action buttons
    public function actionChangeHandler($newAction, BasketExtractorService $basketExtractor)
    {
        // close the current action
        $this->closeCurrentAction();
        $this->activateAction($newAction, $basketExtractor);
    }

    public function parseTextHandler($parserApplication, ImageService $imageService, BasketExtractorService $basketExtractor)
    {
        $this->parserApplication = $parserApplication;
        $this->extractText($imageService, $basketExtractor);
        $this->parseText($basketExtractor);
        $this->emitSelf('action.next');
    }

    // It sets the imagePath parameter to the value passed in and loads the image to the editor.
    public function tempImageHandler($imageName = '')
    {
        $this->imagePath = $imageName;
        if ($this->imagePath != '') {
            $this->emitSelf('action.next');
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

    // Event handler for the image editing done event.
    public function imageEditingHandler($imageData = '', ImageService $imageService)
    {
        $imageService->updateTempImageOfUser($this->imagePath, auth()->user()->id, $imageData);
        $this->emitSelf('action.next');
    }

    public function basketDataUpdateHandler(array $updatedData)
    {
        foreach ($updatedData as $key => $value) {
            $this->basket[$key] = $value;
        }
        $this->basket = ScannedBasket::fromArray($this->basket)->toArray();
        // Notify the components about the changes.
        $this->emit('basket.data.extracted', $this->basket);
        $this->emitSelf('action.next');
    }
    public function basketDataFinishedHandler(ImageService $imageService)
    {
        // Create the basket
        $basket = (new Basket())->create([
            'shop_id' => $this->basket['marketId'],
            'date' => $this->basket['date'],
            'total' => $this->basket['total'],
            'receipt_id' => $this->basket['basketId'],
            'user_id' => auth()->user()->id,
        ]);
        // If we have items, then create the basket items.
        if (count($this->basket['items']) > 0) {
            foreach ($this->basket['items'] as $item) {
                (new BasketItem())->create([
                    'basket_id' => $basket->id,
                    'item_id' => $item['itemId'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'quantity_unit_id' => $item['quantity_unit_id'],
                    'unit_price' => $item['unit_price'],
                ]);
            }
        }
        // redirect to basket edit page
        return $this->addImageToBasket($basket->id, $imageService);
    }

    // It parses the extracted text with the selected parser application.
    private function parseText(BasketExtractorService $basketExtractor)
    {
        $basket = $basketExtractor->parseTextWith($this->rawExtractedText, $this->parserApplication);
        $this->basket = $basket->toArray();

        $this->emit('basket.data.extracted', $this->basket);
    }

    private function closeCurrentAction()
    {
        // close the panel that is connected to the current action
        if (array_key_exists($this->action, self::PANELS_FOR_ACTION)) {
            $this->emit('panel.close', self::PANELS_FOR_ACTION[$this->action]);
        }
    }

    private function activateAction($newAction, BasketExtractorService $basketExtractor)
    {
        $this->action = $newAction;
        switch ($this->action) {
        case self::ACTION_PICK:
            // In this case we have to open the image selection panel
            $this->parserApplication = '';
            $this->imagePath = '';
            $this->emit("panel.open", self::PANEL_PICK_IMAGE);
            break;
        case self::ACTION_EDIT:
            $this->parserApplication = '';
            $this->dispatchBrowserEvent('receiptScan.edit', ['imagePath' => route('image.viewTemp', ['filename' => $this->imagePath, 'v' => time()])]);
            break;
        case self::ACTION_PARSE:
            $this->parserApplication = '';
            $this->rawExtractedText = '';
            $this->emit("panel.open", self::PANEL_PARSER, ['parsers' => $basketExtractor->getParserApplications()]);
            break;
        case self::ACTION_BASKET:
        case self::ACTION_COMPANY:
        case self::ACTION_SHOP:
        case self::ACTION_BASKET_ITEMS:
            $panel = self::PANELS_FOR_ACTION[$this->action];
            $this->emit('panel.update', $panel, [ 'basket' => $this->basket ]);
            $this->emit("panel.open", $panel);
            break;
        }
    }

    private function extractText(ImageService $imageService, BasketExtractorService $basketExtractor)
    {
        $config = $basketExtractor->getParserApplicationConfig($this->parserApplication);
        $ocr = (new TesseractOCR($imageService->tempFilePath($this->imagePath, auth()->user()->id)));
        if (array_key_exists('lang', $config)) {
            $ocr->lang($config['lang']);
        }
        if (array_key_exists('user-pattern-file', $config)) {
            $ocr->userPatterns(base_path('tesseract-user-patterns/'.$config['user-pattern-file']));
        }
        if (array_key_exists('psm', $config)) {
            $ocr->psm($config['psm']);
        }
        if (array_key_exists('oem', $config)) {
            $ocr->oem($config['oem']);
        }
        if (array_key_exists('user-words-file', $config)) {
            $ocr->userWords($config['user-words-file']);
        }
        $this->rawExtractedText = $ocr->run();
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
}
