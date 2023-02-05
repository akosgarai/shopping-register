<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;
use Livewire\WithFileUploads;

use App\Services\ImageService;

class TempImages extends Component
{
    use WithFileUploads;

    public $newImage = null;
    public $uploadedImages = [];

    public function mount(ImageService $imageService)
    {
        $this->uploadedImages = $imageService->listTempImagesFromUserFolder(auth()->user()->id);
    }

    public function render()
    {
        return view('livewire.component.temp-images');
    }

    public function saveNewImage(ImageService $imageService)
    {
        $this->validate([
            'newImage' => 'required|image',
        ]);
        $receiptUrl = $imageService->saveTempImageToUserFolder($this->newImage, auth()->user()->id);
        $this->uploadedImages = $imageService->listTempImagesFromUserFolder(auth()->user()->id);

        $this->emitUp('temp.image', 'load', basename($receiptUrl));
    }

    public function deleteImage(string $imageName, ImageService $imageService)
    {
        $imageService->deleteTempImageFromUserFolder($imageName, auth()->user()->id);
        $this->uploadedImages = $imageService->listTempImagesFromUserFolder(auth()->user()->id);
    }
}
