<?php

namespace App\Http\Livewire\Recipe;

use Alimranahmed\LaraOCR\Services\OcrAbstract;
use Livewire\Component;
use Livewire\WithFileUploads;

class Upload extends Component
{
    use WithFileUploads;

    public $file;

    public $message = '';
    public $parsedText = '';
    public $uploadedImageUrl = '';

    public function render()
    {
        return view('livewire.recipe.upload');
    }

    public function save()
    {
        $this->validate([
            'file' => 'required|image',
        ]);
        // save file to storage/app/public/recipes
        // save file name to database
        $store = $this->file->store('recipes', 'public');
        $ocr = app()->make(OcrAbstract::class);
        $this->parsedText = $ocr->scan(public_path('storage/'.$store));
        $this->uploadedImageUrl = '/storage/'.$store;
        $this->message = 'File saved to ' . $store;
    }
}
