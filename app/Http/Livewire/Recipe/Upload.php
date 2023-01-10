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
    public $showEditor = false;

    // Receipt parameters that has to be extracted from the image
    public $receipt = [
        'id' => '',
        'taxNumber' => '',
        'address' => '',
        'name' => '',
        'date' => '',
        'total' => '',
        'items' => [],
    ];

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
        $this->parseText();
    }

    public function parseText() {
        $this->parseSparReceipt();
        $this->showEditor = true;
    }

    private function parseSparReceipt() {
        // Parse the receipt line by line (delimiter is end of line)
        // Extract the receipt parameters
        $lines = explode("\n", $this->parsedText);
        // the first line is the name of the store
        $this->receipt['name'] = $lines[0];
        // the next 2 line is the address
        $this->receipt['address'] = $lines[1].' '.$lines[2];
        // The tax number is the 7th line. The actual tax number starts after the ':'.
        $this->receipt['taxNumber'] = substr($lines[6], strpos($lines[6], ':')+1);
        $firstLineAfterItems = $this->parseSparItemLines($lines, 7);
        // The total price could be extracted from the line that starts with 'BANKKARTYA' or 'ÖSSZESEN:'
        // The total price is the last number in the line followed by ' Ft'
        // The id could be extracted from the line that starts with 'NYUGTASZAM:'. The id is followed by the ': '.
        // The date could be extracted from line after the id line. The date format is 'YYYY.MM.DD, HH:MM'
        $nextIsDate = false;
        for ($i = $firstLineAfterItems; $i < count($lines); $i++) {
            if ($nextIsDate) {
                $this->receipt['date'] = substr($lines[$i], strpos($lines[$i], ': ')+2);
                $nextIsDate = false;
            }
            if (strpos($lines[$i], 'BANKKARTYA') !== false || strpos($lines[$i], 'ÖSSZESEN:') !== false) {
                $firstSpace = strpos($lines[$i], ' ');
                $lastSpace = strrpos($lines[$i], ' ');
                $this->receipt['total'] = substr($lines[$i], $firstSpace, $lastSpace-$firstSpace);
            }
            if (strpos($lines[$i], 'NYUGTASZAM:') !== false) {
                $this->receipt['id'] = substr($lines[$i], strpos($lines[$i], ': ')+2);
                $nextIsDate = true;
            }
        }
    }

    // Returns the first line index that is not item.
    private function parseSparItemLines($lines, $from)
    {
        // The lines containing the items are following the next pattern:
        // a prefix (3 character) followed by a space. The next characters are the name of the item, followed by a space and the price.
        // Start with the 7.th line. If we found the first line that does not match the pattern, set the itemParsing flag to true.
        // The items are finished, when the itemParsing flag is true and the next line does not match the pattern.
        $itemParsing = false;
        $item = [];
        for ($i = $from; $i < count($lines); $i++) {
            if (preg_match('/^[A-Z]{3} /', $lines[$i])) {
                $itemParsing = true;
                $lastSpaceIndex = strrpos($lines[$i], ' ');
                $item['name'] = substr($lines[$i], 4, $lastSpaceIndex-4);
                $item['price'] = substr($lines[$i], $lastSpaceIndex+1);
                $this->receipt['items'][] = $item;
            } else {
                if ($itemParsing) {
                    return $i;
                }
            }
        }
    }

    private function extractText($fileName)
    {
        $ocr = app()->make(OcrAbstract::class);
        $this->parsedText = $ocr->scan(public_path('storage/recipes/'.$fileName));
    }
}
