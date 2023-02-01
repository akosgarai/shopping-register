<?php

namespace App\Http\Livewire;

use Alimranahmed\LaraOCR\Services\OcrAbstract;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

use App\Models\Basket;
use App\ScannedBasket;
use App\Services\BasketExtractorService;
use App\Services\DataPredictionService;
use App\Services\ImageService;

class ReceiptScan extends Component
{
    use WithFileUploads;

    const ACTION_PICK = 'pick';
    const ACTION_EDIT = 'edit';
    const ACTION_PARSE = 'parse';
    const ACTION_BASKET = 'basket';

    const BASKET_TAB_ID = 'basket-id';
    const BASKET_TAB_SIMILAR = 'basket-same';

    public $action = '';
    public $tempImage = null;
    public $prevTempImages = [];
    public $imagePath = '';
    public $rawExtractedText = '';
    public $parserApplication = '';
    public $basket = [];

    public $basketSuggestions = null;
    public $basketPreview = null;

    public $createBasketTab = self::BASKET_TAB_ID;

    protected $listeners = ['edit.finished' => 'saveEditedImage'];

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
        $this->prevTempImages = $imageService->listTempImagesFromUserFolder(auth()->user()->id);
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

    // Event handler for the offcanvas open event.
    // It sets the action parameter to ACTION_PICK
    // and dispatches the 'receiptScan.pick' browser event to open the offcanvas.
    public function offcanvasOpen()
    {
        $this->action = self::ACTION_PICK;
        $this->imagePath = '';
        $this->parserApplication = '';
        $this->dispatchBrowserEvent('receiptScan.pick');
    }

    // Event handler for the offcanvas close event.
    // In case of the action is ACTION_PICK it sets the action parameter to empty.
    // Otherwise we are in the edit step after submitting the offcanvas, so we have to keep this action.
    public function offcanvasClose()
    {
        if ($this->action == self::ACTION_PICK) {
            $this->action = '';
        }
    }

    // Action and frontend initialization for the edit step.
    // It sets the action parameter to ACTION_EDIT and dispatches the 'receiptScan.edit' browser event
    // to initialize the frontend, loads the image to the editor.
    public function editStep()
    {
        $this->action = self::ACTION_EDIT;
        $this->parserApplication = '';
        $this->dispatchBrowserEvent('receiptScan.edit', ['imagePath' => route('image.viewTemp', ['filename' => $this->imagePath])]);
    }

    // Event handler for the offcanvas submit event.
    // It validates the image and if it is valid it saves it to the temp folder and
    // loads the image to the editor.
    public function saveTempImage(ImageService $imageService)
    {
        try {
            $this->validate([
                'tempImage' => 'required|image',
            ]);
        } catch (ValidationException $e) {
            $messages = $e->validator->getMessageBag();
            $this->dispatchBrowserEvent('model.validation', ['type' => 'pick', 'model' => 'ImageOffcanvas', 'messages' => $messages]);
            return;
        }
        $receiptUrl = $imageService->saveTempImageToUserFolder($this->tempImage, auth()->user()->id);
        $this->prevTempImages = $imageService->listTempImagesFromUserFolder(auth()->user()->id);
        $this->dispatchBrowserEvent('tempImages.refresh', ['images' => $this->prevTempImages]);
        $this->loadTempImage(basename($receiptUrl));
    }

    // It sets the imagePath parameter to the value passed in and loads the image to the editor.
    public function loadTempImage($imageName)
    {
        $this->imagePath = $imageName;
        $this->editStep();
    }

    // Event handler for the editor save event.
    public function saveEditedImage($imageData, ImageService $imageService)
    {
        $imageService->updateTempImageOfUser($this->imagePath, auth()->user()->id, $imageData);
        $this->extractText($imageService);
        $this->action = self::ACTION_PARSE;
    }

    // It parses the extracted text with the selected parser application.
    public function parseText($parserApplication, BasketExtractorService $basketExtractor, DataPredictionService $dataPrediction)
    {
        $this->parserApplication = $parserApplication;
        $basket = $basketExtractor->parseTextWith($this->rawExtractedText, $this->parserApplication);
        $this->basket = $basket->toArray();
        $this->action = self::ACTION_BASKET;
        // check the raw receipt id. If we have exact match, display the same baskets tab.
        $this->basketSuggestions = $dataPrediction->getBasketSuggestions(auth()->user()->id, $this->basket['id'], 10);
        if ($this->basketSuggestions->count() > 0) {
            $firstBasket = $this->basketSuggestions->first();
            if ($firstBasket->distance == 0) {
                $this->createBasketTab = self::BASKET_TAB_SIMILAR;
                $this->basketPreview = $firstBasket;
            }
        }
    }

    public function basketIdForm()
    {
        $this->createBasketTab = self::BASKET_TAB_ID;
        $this->basketPreview = null;
    }

    public function basketSimilarBaskets(DataPredictionService $dataPrediction)
    {
        $this->basketSuggestions = $dataPrediction->getBasketSuggestions(auth()->user()->id, $this->basket['id'], 10);
        $this->createBasketTab = self::BASKET_TAB_SIMILAR;
    }

    public function previewBasketOpen($basketIndex, DataPredictionService $dataPrediction)
    {
        $this->basketSuggestions = $dataPrediction->getBasketSuggestions(auth()->user()->id, $this->basket['id'], 10);
        $this->basketPreview = $this->basketSuggestions[$basketIndex];
    }

    public function previewBasketClose(DataPredictionService $dataPrediction)
    {
        $this->basketSuggestions = $dataPrediction->getBasketSuggestions(auth()->user()->id, $this->basket['id'], 10);
        $this->basketPreview = null;
    }

    public function addImageToBasket(ImageService $imageService)
    {
        // move the image from the temp folder to the receipt folder
        $imageService->moveReceiptImageFromTempToReceiptUserFolder($this->imagePath, auth()->user()->id);
        // and add the image to the basket
        $basket = Basket::where('id', $this->basketPreview->id)
            ->where('user_id', auth()->user()->id)
            ->first();
        if ($basket) {
            $basket->receipt_url = $this->imagePath;
            $basket->save();
        }
        return redirect()->route('basket', ['id' => $this->basketPreview->id]);
    }
    public function changeBasketImage(ImageService $imageService)
    {
        // delete the current image
        $imageService->deleteReceiptImageFromUserFolder($this->basketPreview->receipt_url, auth()->user()->id);
        return $this->addImageToBasket($imageService);
    }

    public function deleteTempImage(string $imageName, ImageService $imageService)
    {
        $imageService->deleteTempImageFromUserFolder($imageName, auth()->user()->id);
        $this->imagePath = '';
        $this->prevTempImages = $imageService->listTempImagesFromUserFolder(auth()->user()->id);
        $this->dispatchBrowserEvent('tempImages.refresh', ['images' => $this->prevTempImages]);
    }

    private function extractText(ImageService $imageService)
    {
        $ocr = app()->make(OcrAbstract::class);
        $this->rawExtractedText = $ocr->scan($imageService->tempFilePath($this->imagePath, auth()->user()->id));
    }
}
