<?php

namespace App\Http\Livewire;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Item;
use App\Models\Shop;
use App\Services\ImageService;

class BasketCrud extends CrudPage
{
    use WithFileUploads;

    public const PANEL_NAME = 'basketPanel';
    public $templateName = 'livewire.basket-crud';

    public $shopId = '';
    public $date = '';
    public $total = 0.0;
    public $receiptId = '';
    public $imageURL = '';
    public $image = null;
    public $items = [];

    public $newItemId = '';
    public $newItemPrice = '';

    protected $listeners = [
        'basket.create' => 'saveNew',
        'basket.update' => 'update',
        'basket.delete' => 'delete',
        'action.back' => 'clearAction',
    ];

    public function delete($modelId)
    {
        $basket = Basket::where('id', $modelId)
            ->withCount('basketItems')
            ->first();
        if ($basket != null && $basket->basket_items_count == 0) {
            $basket->delete();
        }
        parent::clearAction();
    }

    public function getTemplateParameters()
    {
        return [
            'baskets' =>  Basket::where('user_id', auth()->user()->id)
                ->withCount('basketItems')
                ->with(['shop', 'shop.address'])
                ->get(),
            'shopOptions' =>  $this->getShops(),
            'itemOptions' => $this->getItems(),
            'formData' => $this->formData(),
            'panelBasket' => [
                'shopId' => $this->shopId,
                'date' => $this->date,
                'total' => $this->total,
                'receiptId' => $this->receiptId,
                'imageURL' => $this->imageURL,
                'image' => $this->image,
                'id' => $this->modelId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
                'items' => $this->items,
                'newItemId' => $this->newItemId,
                'newItemPrice' => $this->newItemPrice,
            ]
        ];
    }

    public function initialize()
    {
        switch ($this->action) {
            case parent::ACTION_CREATE:
                $this->shopId = '';
                $this->date = '';
                $this->total = 0.0;
                $this->receiptId = '';
                $this->imageURL = '';
                $this->image = null;
                $this->items = [];
                $this->createdAt = '';
                $this->updatedAt = '';
                $this->newBasketItemId = '';
                $this->newBasketItemPrice = '';
                break;
            case parent::ACTION_READ:
            case parent::ACTION_UPDATE:
            case parent::ACTION_DELETE:
                $basket = Basket::where('id', $this->modelId)
                    ->withCount('basketItems')
                    ->with(['shop', 'shop.address', 'basketItems', 'basketItems.item'])
                    ->first();
                $this->shopId = $basket->shop->id;
                $this->date = $basket->date;
                $this->total = $basket->total;
                $this->receiptId = $basket->receipt_id;
                $this->imageURL = !empty($basket->receipt_url) ? route('image.viewReceipt', ['filename' =>  $basket->receipt_url]) : '';
                $this->createdAt = $basket->created_at;
                $this->updatedAt = $basket->updated_at;
                $this->items = $basket->basketItems->toArray();
                break;
        }
        if ($this->action == '') {
            $this->modelId = '';
            $this->emit('panel.close');
            return;
        }
        $this->emit('crudaction.update', [
            'action' => $this->action,
            'basket' => [
                'shopId' => $this->shopId,
                'date' => $this->date,
                'total' => $this->total,
                'receiptId' => $this->receiptId,
                'imageURL' => $this->imageURL,
                'image' => $this->image,
                'id' => $this->modelId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
                'items' => $this->items,
                'newItemId' => $this->newItemId,
                'newItemPrice' => $this->newItemPrice,
            ],
            'formData' => $this->formData(),
            'shops' =>  $this->getShops(),
            'items' => $this->getItems(),
        ]);
        $this->emit('panel.open', self::PANEL_NAME);
    }

    public function saveNew(array $model)
    {
        $imageService = new ImageService();
        $this->updateModelParams($model);
        $this->validate([
            'shopId' => 'required|integer|exists:shops,id',
            'date' => 'required|date',
            'total' => 'required|numeric',
            'receiptId' => 'required|string',
            'image' => 'nullable|image',
        ]);
        if ($this->image) {
            $receiptUrl = $imageService->saveReceiptImageToUserFolder($this->image, auth()->user()->id);
        }
        $basket = Basket::firstOrCreate([
            'shop_id' => $this->shopId,
            'date' => $this->date,
            'total' => $this->total,
            'receipt_id' => $this->receiptId,
            'user_id' => auth()->user()->id,
            'receipt_url' => $receiptUrl,
        ]);
        // save the basket items
        foreach ($this->items as $basketItem) {
            BasketItem::firstOrCreate([
                'item_id' => $basketItem['item_id'],
                'price' => $basketItem['price'],
                'basket_id' => $basket->id,
            ]);
        }
        $this->modelId = $basket->id;
        $this->setAction(parent::ACTION_UPDATE);
    }

    public function update(array $model)
    {
        $imageService = new ImageService();
        $this->updateModelParams($model);
        $this->validate([
            'modelId' => 'required|integer|exists:baskets,id',
            'shopId' => 'required|integer|exists:shops,id',
            'date' => 'required|date',
            'total' => 'required|numeric',
            'receiptId' => 'required|string',
            'image' => 'nullable|image',
        ]);
        if ($this->image) {
            $receiptUrl = $imageService->saveReceiptImageToUserFolder($this->image, auth()->user()->id);
        }
        if ($this->basketImage) {
            $this->basketImageURL = '/storage/'.$this->basketImage->store('receipts', 'public');
        }
        Basket::where('id', $this->modelId)->where('user_id', auth()->user()->id)->update([
            'shop_id' => $this->shopId,
            'date' => $this->date,
            'total' => $this->total,
            'receipt_id' => $this->receiptId,
            'receipt_url' => $receiptUrl,
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
        $this->setAction(parent::ACTION_UPDATE);
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

    private function updateModelParams(array $model)
    {
        if (array_key_exists('shopId', $model)) {
            $this->shopId = $model['shopId'];
        }
        if (array_key_exists('date', $model)) {
            $this->date = $model['date'];
        }
        if (array_key_exists('total', $model)) {
            $this->total = $model['total'];
        }
        if (array_key_exists('receiptId', $model)) {
            $this->receiptId = $model['receiptId'];
        }
        if (array_key_exists('imageURL', $model)) {
            $this->imageURL = $model['imageURL'];
        }
        if (array_key_exists('items', $model)) {
            $this->items = $model['items'];
        }
        if (array_key_exists('image', $model)) {
            $this->image = $model['image'];
        }
        if (array_key_exists('newItemId', $model)) {
            $this->newItemId = $model['newItemId'];
        }
        if (array_key_exists('newItemPrice', $model)) {
            $this->newItemPrice = $model['newItemPrice'];
        }
    }

    private function getShops()
    {
        return Shop::with('address')->get();
    }

    private function getItems()
    {
        return Item::all();
    }

    private function formData() {
        $items = $this->getItems();
        return [
            ['keyName' => 'shopId', 'type' => 'selectorshop', 'rules' => 'required|integer|exists:shops,id', 'readonly' => false, 'options' => $this->getShops()],
            ['keyName' => 'date', 'type' => 'datetimelocalinput', 'label' => __('Date'), 'rules' => 'required|date', 'readonly' => false],
            ['keyName' => 'receiptId', 'type' => 'textinput', 'label' => __('Receipt ID'), 'rules' => 'required|string', 'readonly' => false],
            ['keyName' => 'items', 'type' => 'itemlist', 'currentItems' => $this->items, 'options' => $items],
            ['keyNameItem' => 'newItemId', 'type' => 'basketitem', 'keyNamePrice' => 'newItemPrice', 'options' => $items],

            ['keyName' => 'createdAt', 'type' => 'textinput', 'label' => __('Created'), 'rules' => '', 'readonly' => true],
            ['keyName' => 'updatedAt', 'type' => 'textinput', 'label' => __('Updated'), 'rules' => '', 'readonly' => true],
        ];
    }
}
