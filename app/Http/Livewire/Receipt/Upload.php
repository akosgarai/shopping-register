<?php

namespace App\Http\Livewire\Receipt;

use Alimranahmed\LaraOCR\Services\OcrAbstract;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

use App\Services\Parser\SparParserService;

class Upload extends Component
{
    public $file;
    public $uploadedImageUrl = '';
    public $parsedText = '';
    public $showEditor = false;

    // Receipt parameters that has to be extracted from the image
    public $receipt = [
        'id' => '',
        'taxNumber' => '',
        'marketAddress' => '',
        'marketName' => '',
        'companyAddress' => '',
        'companyName' => '',
        'date' => '',
        'total' => '',
        'items' => [],
    ];

    protected $listeners = ['edit.finished' => 'storeEditedImage'];

    public function render()
    {
        return view('livewire.receipt.upload');
    }

    public function storeEditedImage($filepath, $imageData)
    {
        $image = $imageData;  // your base64 encoded
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);

        $fileName = basename($filepath);
        $result = Storage::disk('public')->put('receipts/'.$fileName, base64_decode($image));
        $this->uploadedImageUrl = Storage::disk('public')->url('receipts/'.$fileName);
        // Delete the temporary file
        $this->parsedText = $filepath;
        Storage::disk('public')->delete('tmp/'.$fileName);
        // Extract the text from the image
        $this->extractText($fileName);
        $this->parseText();
    }

    public function parseText() {
        $this->receipt = (new SparParserService($this->parsedText))->parse();
        $this->showEditor = true;
    }

    private function extractText($fileName)
    {
        $ocr = app()->make(OcrAbstract::class);
        $this->parsedText = $ocr->scan(public_path('storage/receipts/'.$fileName));
    }

    private function initReceipt() {
        $this->receipt = [
            'id' => '',
            'taxNumber' => '',
            'marketAddress' => '',
            'marketName' => '',
            'companyAddress' => '',
            'companyName' => '',
            'date' => '',
            'total' => '',
            'items' => [],
        ];
    }
}
