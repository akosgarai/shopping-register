<?php

namespace App\Http\Livewire;

use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;

use App\Models\Basket;
use App\Models\Shop;

class BasketCrud extends OffcanvasPage
{
    use WithFileUploads;

    public $templateName = 'livewire.basket-crud';

    public $basketShop = '';
    public $basketDate = '';
    public $basketTotal = '';
    public $basketReceiptId = '';
    public $basketImageURL = '';
    public $basketImage = null;

    protected $listeners = ['offcanvasClose'];

    public function load($id)
    {
        $this->modelId = $id;
        $this->action = parent::ACTION_UPDATE;
        $basket = Basket::find($this->modelId);
        $this->basketShop = $basket->shop_id;
        $this->basketDate = $basket->date;
        $this->basketTotal = $basket->total;
        $this->basketReceiptId = $basket->receipt_id;
        $this->basketImageURL = $basket->receipt_url;
        $this->createdAt = $basket->created_at;
        $this->updatedAt = $basket->updated_at;
        // dispatch a browser event to update the image on the offcanvas.
        // The name of the event id 'basket-image' and the parameter is the URL of the image.
        $this->dispatchBrowserEvent('basket-image', ['url' => $basket->receipt_url]);
    }

    public function getTemplateParameters()
    {
        return [
            'baskets' =>  Basket::where('user_id', auth()->user()->id)->get(),
            'shops' =>  Shop::all()
        ];
    }

    public function initialize()
    {
        $this->modelId = '';
        $this->basketShop = '';
        $this->basketDate = '';
        $this->basketTotal = '';
        $this->basketReceiptId = '';
        $this->basketImageURL = '';
        $this->basketImage = null;
        $this->createdAt = '';
        $this->updatedAt = '';
    }

    public function saveNew()
    {
        try {
            $this->validate([
                'basketShop' => 'required|integer|exists:shops,id',
                'basketDate' => 'required|date',
                'basketTotal' => 'required|numeric',
                'basketReceiptId' => 'required|string',
                'basketImage' => 'nullable|image',
            ]);
        } catch (ValidationException $e) {
            $messages = $e->validator->getMessageBag();
            $this->dispatchBrowserEvent('model.validation', ['type' => 'new', 'model' => 'Basket', 'messages' => $messages]);
            return;
        }
        $receiptUrl = null;
        if ($this->basketImage) {
            $receiptUrl = '/storage/'.$this->basketImage->store('receipts', 'public');
        }
        $basket = Basket::firstOrCreate([
            'shop_id' => $this->basketShop,
            'date' => $this->basketDate,
            'total' => $this->basketTotal,
            'receipt_id' => $this->basketReceiptId,
            'user_id' => auth()->user()->id,
            'receipt_url' => $receiptUrl,
        ]);
        return redirect()->route('basket', ['action' => 'update', 'id' => $basket->id]);
    }

    public function update()
    {
        try {
            $this->validate([
                'modelId' => 'required|integer|exists:baskets,id',
                'basketShop' => 'required|integer|exists:shops,id',
                'basketDate' => 'required|date',
                'basketTotal' => 'required|numeric',
                'basketReceiptId' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            $messages = $e->validator->getMessageBag();
            $this->dispatchBrowserEvent('model.validation', ['type' => 'update', 'model' => 'Basket', 'messages' => $messages]);
            return;
        }
        if ($this->basketImage) {
            $this->basketImageURL = $this->basketImage->store('receipts', 'public');
        }
        Basket::where('id', $this->modelId)->where('user_id', auth()->user()->id)->update([
            'shop_id' => $this->basketShop,
            'date' => $this->basketDate,
            'total' => $this->basketTotal,
            'receipt_id' => $this->basketReceiptId,
            'receipt_url' => $this->basketImageURL,

        ]);
        return redirect()->route('basket', ['action' => 'update', 'id' => $this->basketId]);
    }

    public function delete($id)
    {
        $basket = Basket::find($id);
        if ($basket != null && $basket->basketItems->count() == 0) {
            $basket->delete();
        }
    }
}
