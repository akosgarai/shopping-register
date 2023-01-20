<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Basket;
use App\Models\Shop;

class BasketCrud extends Component
{
    public $action = '';
    public $basketId = '';
    public $basketShop = '';
    public $basketDate = '';
    public $basketTotal = '';
    public $basketReceiptId = '';
    public $createdAt = '';
    public $updatedAt = '';

    protected $queryString = [
        'action' => ['except' => ''],
        'basketId' => ['except' => '', 'as' => 'id'],
    ];

    public function mount()
    {
        $this->action = request()->query('action', '');
        $id = request()->query('id', '');
        if ($id != '') {
            $this->loadBasket($id);
        }
    }

    public function loadBasket($id)
    {
        $this->basketId = $id;
        $this->action = 'update';
        $basket = Basket::find($this->basketId);
        $this->basketShop = $basket->shop_id;
        $this->basketDate = $basket->date;
        $this->basketTotal = $basket->total;
        $this->basketReceiptId = $basket->receipt_id;
        $this->createdAt = $basket->created_at;
        $this->updatedAt = $basket->updated_at;
    }

    public function render()
    {
        return view('livewire.basket-crud', [
            'baskets' =>  Basket::where('user_id', auth()->user()->id)->get(),
            'shops' =>  Shop::all()
        ])->extends('layouts.app');
    }

    public function setAction($action)
    {
        $this->action = $action;
        if ($action != 'update') {
            $this->basketId = '';
            $this->basketShop = '';
            $this->basketDate = '';
            $this->basketTotal = '';
            $this->basketReceiptId = '';
            $this->createdAt = '';
            $this->updatedAt = '';
        }
    }

    public function saveNewBasket()
    {
        $this->validate([
            'basketShop' => 'required|integer|exists:shops,id',
            'basketDate' => 'required|date',
            'basketTotal' => 'required|numeric',
            'basketReceiptId' => 'required|string',
        ]);
        $basket = Basket::firstOrCreate([
            'shop_id' => $this->basketShop,
            'date' => $this->basketDate,
            'total' => $this->basketTotal,
            'receipt_id' => $this->basketReceiptId,
            'user_id' => auth()->user()->id,
        ]);
        return redirect()->route('basket', ['action' => 'update', 'id' => $basket->id]);
    }

    public function updateBasket()
    {
        $this->validate([
            'basketId' => 'required|integer|exists:baskets,id',
            'basketShop' => 'required|integer|exists:shops,id',
            'basketDate' => 'required|date',
            'basketTotal' => 'required|numeric',
            'basketReceiptId' => 'required|string',
        ]);
        Basket::where('id', $this->basketId)->where('user_id', auth()->user()->id)->update([
            'shop_id' => $this->basketShop,
            'date' => $this->basketDate,
            'total' => $this->basketTotal,
            'receipt_id' => $this->basketReceiptId,
        ]);
        return redirect()->route('basket', ['action' => 'update', 'id' => $this->basketId]);
    }

    public function deleteBasket($id)
    {
        $basket = Basket::find($id);
        if ($basket != null && $basket->basketItems->count() == 0) {
            $basket->delete();
        }
    }
}
