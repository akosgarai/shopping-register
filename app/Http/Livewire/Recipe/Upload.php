<?php

namespace App\Http\Livewire\Recipe;

use Alimranahmed\LaraOCR\Services\OcrAbstract;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Upload extends Component
{
    public $file;
    public $uploadedImageUrl = '';
    public $parsedText = '';

    protected $listeners = ['edit.finished' => 'storeEditedImage'];

    public function render()
    {
        return view('livewire.recipe.upload');
    }

    public function storeEditedImage($filepath, $imageData)
    {
        $image = $imageData;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);

        $fileName = basename($filepath);
        $result = Storage::disk('public')->put('recipes/'.$fileName, base64_decode($image));
        $this->uploadedImageUrl = Storage::disk('public')->url('recipes/'.$fileName);
        // Delete the temporary file
        $this->parsedText = $filepath;
        Storage::disk('public')->delete('tmp/'.$fileName);
        // Extract the text from the image
        $this->extractText($fileName);
    }

    private function extractText($fileName)
    {
        $ocr = app()->make(OcrAbstract::class);
        $this->parsedText = $ocr->scan(public_path('storage/recipes/'.$fileName));
    }
}
