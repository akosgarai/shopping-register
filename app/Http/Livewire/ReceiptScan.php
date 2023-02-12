<?php

namespace App\Http\Livewire;

use Alimranahmed\LaraOCR\Services\OcrAbstract;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

use App\Models\Basket;
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

    const ACTION_STEP = [
        self::ACTION_PICK,
        self::ACTION_EDIT,
        self::ACTION_PARSE,
        self::ACTION_BASKET,
        self::ACTION_COMPANY,
        self::ACTION_SHOP,
    ];

    const PANEL_PICK_IMAGE = 'pickImagePanel';
    const PANEL_PARSER = 'parsersPanel';
    const PANEL_BASKET_ID = 'basketIDPanel';
    const PANEL_BASKET_COMPANY = 'basketCompanyPanel';
    const PANEL_BASKET_SHOP = 'basketShopPanel';

    const PANELS_FOR_ACTION = [
        self::ACTION_PICK => self::PANEL_PICK_IMAGE,
        self::ACTION_BASKET => self::PANEL_BASKET_ID,
        self::ACTION_COMPANY => self::PANEL_BASKET_COMPANY,
        self::ACTION_SHOP => self::PANEL_BASKET_SHOP,
        self::ACTION_PARSE => self::PANEL_PARSER,
    ];

    const PARSERS = [
        [ 'name' => 'spar', 'label' => 'Spar'],
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
        if ($step) {
            $this->extractText($imageService);

            if ($step >= array_search(self::ACTION_BASKET, self::ACTION_STEP)) {
                $this->parseText($this->parserApplication, $basketExtractor);
            }
        }
        if ($this->action != '') {
            $this->activateAction($this->action, $imageService);
        }
    }

    public function render()
    {
        return view('livewire.receipt-scan')->extends('layouts.app');
    }

    // action next closes the current action and moves to the next action
    public function actionNextHandler(ImageService $imageService)
    {
        $currentActionIndex = array_search($this->action, self::ACTION_STEP);
        $this->actionChangeHandler(self::ACTION_STEP[$currentActionIndex + 1], $imageService);
    }
    // action next closes the current action and moves to the next action
    public function actionBackHandler(ImageService $imageService)
    {
        $currentActionIndex = array_search($this->action, self::ACTION_STEP);
        if ($currentActionIndex == 0) {
            $this->closeCurrentAction();
            $this->action = '';
            return;
        }
        $this->actionChangeHandler(self::ACTION_STEP[$currentActionIndex - 1], $imageService);
    }

    // action change handler is for the action buttons
    public function actionChangeHandler($newAction, ImageService $imageService)
    {
        // close the current action
        $this->closeCurrentAction();
        $this->activateAction($newAction, $imageService);
    }

    public function parseTextHandler($parserApplication, BasketExtractorService $basketExtractor)
    {
        $this->parseText($parserApplication, $basketExtractor);
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
        $this->extractText($imageService);
        $this->emitSelf('action.next');
    }

    public function basketDataUpdateHandler($dataName, $newValue)
    {
        $this->basket[$dataName] = $newValue;
        $this->basket = ScannedBasket::fromArray($this->basket)->toArray();
        // Notify the components about the changes.
        $this->emit('basket.data.extracted', $this->basket);
        $this->emitSelf('action.next');
    }

    // It parses the extracted text with the selected parser application.
    private function parseText($parserApplication, BasketExtractorService $basketExtractor)
    {
        $this->parserApplication = $parserApplication;
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

    private function activateAction($newAction, ImageService $imageService)
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
            $this->extractText($imageService);
            $this->emit("panel.open", self::PANEL_PARSER, ['parsers' => self::PARSERS]);
            break;
        case self::ACTION_BASKET:
        case self::ACTION_COMPANY:
        case self::ACTION_SHOP:
            $panel = self::PANELS_FOR_ACTION[$this->action];
            $this->emit('panel.update', $panel, [ 'basket' => $this->basket ]);
            $this->emit("panel.open", $panel);
            break;
        }
    }

    private function extractText(ImageService $imageService)
    {
        $ocr = app()->make(OcrAbstract::class);
        $this->rawExtractedText = $ocr->scan($imageService->tempFilePath($this->imagePath, auth()->user()->id));
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
