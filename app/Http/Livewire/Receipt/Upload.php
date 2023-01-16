<?php

namespace App\Http\Livewire\Receipt;

use Alimranahmed\LaraOCR\Services\OcrAbstract;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

use App\Models\Address;
use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Company;
use App\Models\Item;
use App\Models\Shop;
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

    protected $rules = [
        'receipt.id' => 'required',
        'receipt.taxNumber' => 'required',
        'receipt.marketAddress' => 'required',
        'receipt.marketName' => 'required',
        'receipt.companyAddress' => 'required',
        'receipt.companyName' => 'required',
        'receipt.date' => 'required',
        'receipt.total' => 'required',
        'receipt.items' => 'required',
    ];

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

    public function parseText()
    {
        $this->receipt = (new SparParserService($this->parsedText))->parse();
        $this->showEditor = true;
    }

    public function submit()
    {
        $this->validate();

        // First handle the addresses to get the ids
        $companyAddress = Address::firstOrCreate([
            'raw' => $this->receipt['companyAddress'],
        ]);
        $marketAddress = Address::firstOrCreate([
            'raw' => $this->receipt['marketAddress'],
        ]);
        // Then handle the company and then the shop
        $company = Company::firstOrCreate([
            'name' => $this->receipt['companyName'],
            'tax_number' => $this->receipt['taxNumber'],
            'address_id' => $companyAddress->id,
        ]);
        $shop = Shop::firstOrCreate([
            'name' => $this->receipt['marketName'],
            'address_id' => $marketAddress->id,
            'company_id' => $company->id,
        ]);
        // Then create the basket
        $basket = Basket::create([
            'shop_id' => $shop->id,
            'date' => $this->receipt['date'],
            'total' => $this->receipt['total'],
            'receipt_id' => $this->receipt['id'],
            'user_id' => auth()->user()->id,
        ]);
        // If we have items, handle them
        // then create the basket items.
        if (count($this->receipt['items']) > 0) {
            foreach ($this->receipt['items'] as $item) {
                $product = Item::firstOrCreate([
                    'name' => $item['name'],
                ]);
                BasketItem::create([
                    'basket_id' => $basket->id,
                    'item_id' => $product->id,
                    'price' => $item['price'],
                ]);
            }
        }
    }

    private function extractText($fileName)
    {
        $ocr = app()->make(OcrAbstract::class);
        $this->parsedText = $ocr->scan(public_path('storage/receipts/'.$fileName));
    }

    private function initReceipt()
    {
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
