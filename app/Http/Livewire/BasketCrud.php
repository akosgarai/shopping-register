<?php

namespace App\Http\Livewire;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Item;
use App\Models\Shop;

class BasketCrud extends OffcanvasPage
{
    use WithFileUploads;

    public $templateName = 'livewire.basket-crud';

    public $basketShop = '';
    public $basketDate = '';
    public $basketTotal = 0.0;
    public $basketReceiptId = '';
    public $basketImageURL = '';
    public $basketImage = null;
    public $basketItems = [];
    public $newBasketItemId = '';
    public $newBasketItemPrice = '';

    protected $listeners = ['offcanvasClose', 'deleteBasketItem'];

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
        $this->basketItems = $basket->basketItems->toArray();
        // dispatch a browser event to update the image on the offcanvas.
        // The name of the event id 'basket-image' and the parameter is the URL of the image.
        $this->dispatchBrowserEvent('basket.loaded', ['url' => $basket->receipt_url, 'items' => $basket->basketItems]);
    }

    public function getTemplateParameters()
    {
        return [
            'baskets' =>  Basket::where('user_id', auth()->user()->id)->get(),
            'shops' =>  Shop::all(),
            'items' => Item::all()
        ];
    }

    public function initialize()
    {
        $this->modelId = '';
        $this->basketShop = '';
        $this->basketDate = '';
        $this->basketTotal = 0.0;
        $this->basketReceiptId = '';
        $this->basketImageURL = '';
        $this->basketImage = null;
        $this->basketItems = [];
        $this->createdAt = '';
        $this->updatedAt = '';
        $this->newBasketItemId = '';
        $this->newBasketItemPrice = '';
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
        // save the basket items
        foreach ($this->basketItems as $basketItem) {
            BasketItem::firstOrCreate([
                'item_id' => $basketItem['item_id'],
                'price' => $basketItem['price'],
                'basket_id' => $basket->id,
            ]);
        }
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
            $this->basketImageURL = '/storage/'.$this->basketImage->store('receipts', 'public');
        }
        Basket::where('id', $this->modelId)->where('user_id', auth()->user()->id)->update([
            'shop_id' => $this->basketShop,
            'date' => $this->basketDate,
            'total' => $this->basketTotal,
            'receipt_id' => $this->basketReceiptId,
            'receipt_url' => $this->basketImageURL,

        ]);
        // delete the current basket items and then save the new ones
        BasketItem::where('basket_id', $this->modelId)->delete();
        foreach ($this->basketItems as $basketItem) {
            BasketItem::firstOrCreate([
                'item_id' => $basketItem['item_id'],
                'price' => $basketItem['price'],
                'basket_id' => $this->modelId,
            ]);
        }
        return redirect()->route('basket', ['action' => 'update', 'id' => $this->modelId]);
    }

    public function delete($id)
    {
        $basket = Basket::find($id);
        if ($basket != null && $basket->basketItems->count() == 0) {
            $basket->delete();
        }
    }

    public function addBasketItem()
    {
        try {
            $this->validate([
                'newBasketItemId' => 'required|integer|exists:items,id',
                'newBasketItemPrice' => 'required|numeric',
            ]);
        } catch (ValidationException $e) {
            $messages = $e->validator->getMessageBag();
            $type = $this->modelId == '' ? 'new' : 'update';
            $this->dispatchBrowserEvent('model.validation', ['type' => $type, 'model' => 'Basket', 'messages' => $messages]);
            return;
        }
        $this->basketItems[] = [
            'item_id' => $this->newBasketItemId,
            'price' => $this->newBasketItemPrice,
            'basket_id' => $this->modelId,
        ];
        $insertedIndex = array_key_last($this->basketItems);
        // increase the total with the new item price.
        $this->basketTotal += $this->newBasketItemPrice;
        $this->dispatchBrowserEvent('basketItem.added', [
            'basketItemIndex' => $insertedIndex,
            'selectedItemId' => $this->newBasketItemId,
            'itemPrice' => $this->newBasketItemPrice,
            'buttonLabel' => __('Delete'),
        ]);
        $this->newBasketItemId = '';
        $this->newBasketItemPrice = '';
    }

    public function deleteBasketItem($index)
    {
        // decrease the total with the deleted item price.
        $this->basketTotal -= $this->basketItems[$index]['price'];
        unset($this->basketItems[$index]);
        $this->dispatchBrowserEvent('basketItem.removed', ['basketItemIndex' => $index]);
    }

    public function deleteBasketImage()
    {
        // delete the image from the storage and the database
        $basket = Basket::find($this->modelId);
        if ($basket != null) {
            // delete from the storage
            Storage::disk('public')->delete(str_replace('/storage/', '', $basket->receipt_url));
            $basket->receipt_url = null;
            $basket->save();
            $this->basketImageURL = null;
        }
        $this->basketImageURL = '';
        $this->basketImage = null;
    }
}
