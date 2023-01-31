<?php

namespace App\Http\Livewire;

use Alimranahmed\LaraOCR\Services\OcrAbstract;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

use App\Services\ImageService;

class ReceiptScan extends Component
{
    use WithFileUploads;

    const ACTION_PICK = 'pick';
    const ACTION_EDIT = 'edit';
    const ACTION_PARSE = 'parse';
    const ACTION_BASKET = 'basket';

    public $action = '';
    public $tempImage = null;
    public $prevTempImages = [];
    public $imagePath = '';
    public $rawExtractedText = '';

    protected $listeners = ['edit.finished' => 'saveEditedImage'];

    // The query string parameters.
    protected $queryString = [
        'action' => ['except' => ''],
        'imagePath' => ['except' => '', 'as' => 'document'],
    ];

    // Initialize the component based on the query string parameters.
    public function mount(ImageService $imageService)
    {
        $this->action = request()->query('action', '');
        $this->prevTempImages = $imageService->listTempImageeFromUserFolder(auth()->user()->id);
        if ($this->action == self::ACTION_PARSE) {
            $this->extractText($imageService);
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

    private function extractText(ImageService $imageService)
    {
        $ocr = app()->make(OcrAbstract::class);
        $this->rawExtractedText = $ocr->scan($imageService->tempFilePath($this->imagePath, auth()->user()->id));
    }
}
