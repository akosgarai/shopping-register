<?php

namespace App\Http\Livewire;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Item;
use App\Models\Shop;

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
    public $originalImage = '';
    public $items = [];

    public $newItemId = '';
    public $newItemPrice = '';

    protected $listeners = [
        'basket.create' => 'saveNew',
        'basket.update' => 'update',
        'basket.delete' => 'delete',
        'basket.addBasketItem' => 'addBasketItem',
        'basket.deleteBasketItem' => 'deleteBasketItem',
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
        if ($this->modelId != '') {
            $this->viewData = $this->getBasket()->toArray();
        }
        return [
            'baskets' =>  Basket::where('user_id', auth()->user()->id)
                ->withCount('basketItems')
                ->with(['shop', 'shop.address'])
                ->get(),
            'shopOptions' =>  $this->getShops(),
            'itemOptions' => $this->getItems(),
            'formData' => $this->formData(),
            'viewData' => $this->viewData,
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
                $this->originalImage = '';
                $this->image = null;
                $this->items = [];
                $this->createdAt = '';
                $this->updatedAt = '';
                $this->newBasketItemId = '';
                $this->newBasketItemPrice = '';
                $this->viewData = null;
                break;
            case parent::ACTION_READ:
            case parent::ACTION_UPDATE:
            case parent::ACTION_DELETE:
                $basket = $this->getBasket();
                $this->shopId = $basket->shop->id;
                $this->date = $basket->date;
                $this->total = $basket->total;
                $this->receiptId = $basket->receipt_id;
                $this->imageURL = !empty($basket->receipt_url) ? route('image.viewReceipt', ['filename' =>  $basket->receipt_url]) : '';
                $this->originalImage = $basket->receipt_url;
                $this->createdAt = $basket->created_at;
                $this->updatedAt = $basket->updated_at;
                $this->items = $basket->basketItems->toArray();
                $this->viewData = $basket->toArray();
                break;
        }
        if ($this->action == '') {
            $this->modelId = '';
            $this->emit('panel.close');
            return;
        }
        $this->emitUpdateBasketEvent();
        $this->emit('panel.open', self::PANEL_NAME);
    }

    public function saveNew(array $model)
    {
        $this->updateModelParams($model);
        if (is_null($this->image)) {
            $this->image = $this->originalImage;
        }
        $this->validate([
            'shopId' => 'required|integer|exists:shops,id',
            'date' => 'required|date',
            'total' => 'required|numeric',
            'receiptId' => 'required|string',
            'image' => 'string',
        ]);
        $basket = (new Basket())->firstOrCreate([
            'shop_id' => $this->shopId,
            'date' => $this->date,
            'total' => $this->total,
            'receipt_id' => $this->receiptId,
            'user_id' => auth()->user()->id,
            'receipt_url' => $this->image ?? '',
        ]);
        // save the basket items
        foreach ($this->items as $basketItem) {
            (new BasketItem())->firstOrCreate([
                'item_id' => $basketItem['item_id'],
                'price' => $basketItem['price'],
                'basket_id' => $basket->id,
            ]);
        }
        if ($this->image == $this->originalImage) {
            $this->image = null;
        }
        $this->modelId = $basket->id;
        $this->setAction(parent::ACTION_UPDATE);
    }

    public function update(array $model)
    {
        $this->updateModelParams($model);
        if (is_null($this->image)) {
            $this->image = $this->originalImage;
        }
        $this->validate([
            'modelId' => 'required|integer|exists:baskets,id',
            'shopId' => 'required|integer|exists:shops,id',
            'date' => 'required|date',
            'total' => 'required|numeric',
            'receiptId' => 'required|string',
            'image' => 'string',
        ]);
        Basket::where('id', $this->modelId)->where('user_id', auth()->user()->id)->update([
            'shop_id' => $this->shopId,
            'date' => $this->date,
            'total' => $this->total,
            'receipt_id' => $this->receiptId,
            'receipt_url' => $this->image,
        ]);
        // delete the current basket items and then save the new ones
        BasketItem::where('basket_id', $this->modelId)->delete();
        foreach ($this->items as $basketItem) {
            (new BasketItem())->firstOrCreate([
                'item_id' => $basketItem['item_id'],
                'price' => $basketItem['price'],
                'basket_id' => $this->modelId,
            ]);
        }
        if ($this->image == $this->originalImage) {
            $this->image = null;
        }
        $this->setAction(parent::ACTION_UPDATE);
    }

    public function addBasketItem($itemId, $price)
    {
        $this->newItemId = $itemId;
        $this->newItemPrice = $price;
        $this->validate([
            'newItemId' => 'required|integer|exists:items,id',
            'newItemPrice' => 'required|numeric',
        ]);
        $this->items[] = [
            'item_id' => $this->newItemId,
            'price' => $this->newItemPrice,
            'basket_id' => $this->modelId,
            'item' => Item::where('id', $this->newItemId)->first(),
        ];
        // increase the total with the new item price.
        $this->total += $this->newItemPrice;
        $this->newItemId = '';
        $this->newItemPrice = '';
        $this->correctPrice();
        $this->emitUpdateBasketEvent();
    }

    public function deleteBasketItem($index)
    {
        // decrease the total with the deleted item price.
        $this->total -= $this->items[$index]['price'];
        unset($this->items[$index]);
        $this->emitUpdateBasketEvent();
    }

    public function deleteBasketImage()
    {
        // delete the image from the storage and the database
        $basket = Basket::where('id', $this->modelId)->first();
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
        $params = ['shopId', 'date', 'total', 'receiptId', 'imageURL', 'items', 'image', 'newItemId', 'newItemPrice'];
        foreach ($params as $param) {
            if (array_key_exists($param, $model)) {
                $this->$param = $model[$param];
            }
        }
        if (array_key_exists('uploadedImage', $model)) {
            $this->image = $model['uploadedImage'];
        }
        $this->correctPrice();
    }

    private function getShops()
    {
        return Shop::with('address')->get();
    }

    private function getItems()
    {
        return (new Item())->all();
    }

    private function formData()
    {
        $items = $this->getItems();
        $imageInputLabel = ($this->imageURL == '') ? __('Upload image') : __('Change image');
        return [
            ['keyName' => 'shopId', 'type' => 'selectorshop', 'rules' => 'required|integer|exists:shops,id', 'readonly' => false, 'options' => $this->getShops()],
            ['keyName' => 'date', 'type' => 'datetimelocalinput', 'label' => __('Date'), 'rules' => 'required|date', 'readonly' => false],
            ['keyName' => 'receiptId', 'type' => 'textinput', 'label' => __('Receipt ID'), 'rules' => 'required|string', 'readonly' => false],
            ['keyName' => 'items', 'type' => 'itemlist', 'currentItems' => $this->items, 'options' => $items, 'rules' => ''],
            ['keyNameItem' => 'newItemId', 'type' => 'basketitem', 'keyNamePrice' => 'newItemPrice', 'options' => $items, 'rulesItem' => 'integer|exists:items,id', 'rulesPrice' => 'numeric'],
            ['keyName' => 'total', 'type' => 'textinput', 'label' => __('Total'), 'rules' => 'required|numeric', 'readonly' => count($this->items) > 0],

            ['keyName' => 'createdAt', 'type' => 'textinput', 'label' => __('Created'), 'rules' => '', 'readonly' => true],
            ['keyName' => 'updatedAt', 'type' => 'textinput', 'label' => __('Updated'), 'rules' => '', 'readonly' => true],
            ['keyName' => 'image', 'type' => 'imageinput', 'imageURL' => $this->imageURL, 'rules' => 'nullable|image', 'readonly' => false, 'label' => $imageInputLabel, 'target' => 'uploadedImage'],
        ];
    }

    private function emitUpdateBasketEvent()
    {
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
            'viewData' => $this->viewData,
        ]);
    }

    private function correctPrice()
    {
        // If the number of items is 0, then the total should be the value provided by the user.
        if (count($this->items) == 0) {
            return;
        }
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['price'];
        }
        $this->total = $total;
    }

    private function getBasket()
    {
        return Basket::where('id', $this->modelId)
            ->withCount('basketItems')
            ->with(['shop', 'shop.address', 'shop.company', 'shop.company.address', 'basketItems', 'basketItems.item'])
            ->first();
    }
}
