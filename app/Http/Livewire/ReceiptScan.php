<?php

namespace App\Http\Livewire;

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
        $this->imagePath = basename($receiptUrl);
        $this->action = self::ACTION_EDIT;
        $this->dispatchBrowserEvent('receiptScan.edit');
    }

    public function loadTempImage($imageName)
    {
        $this->imagePath = $imageName;
        $this->action = self::ACTION_EDIT;
        $this->dispatchBrowserEvent('receiptScan.edit');
    }
}
