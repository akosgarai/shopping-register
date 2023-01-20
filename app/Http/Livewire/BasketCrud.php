<?php

namespace App\Http\Livewire;

use Livewire\WithFileUploads;

use App\Models\Basket;
use App\Models\Shop;

class BasketCrud extends OffcanvasPage
{
    use WithFileUploads;

    public $action = '';
    public $basketId = '';
    public $basketShop = '';
    public $basketDate = '';
    public $basketTotal = '';
    public $basketReceiptId = '';
    public $basketImageURL = '';
    public $basketImage = null;
    public $createdAt = '';
    public $updatedAt = '';

    protected $listeners = ['offcanvasClose'];

    protected $queryString = [
        'action' => ['except' => ''],
        'basketId' => ['except' => '', 'as' => 'id'],
    ];

    public function load($id)
    {
        $this->basketId = $id;
        $this->action = 'update';
        $basket = Basket::find($this->basketId);
        $this->basketShop = $basket->shop_id;
        $this->basketDate = $basket->date;
        $this->basketTotal = $basket->total;
        $this->basketReceiptId = $basket->receipt_id;
        $this->basketImageURL = $basket->receipt_url;
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

    public function initialize()
    {
        $this->basketId = '';
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
        $this->validate([
            'basketShop' => 'required|integer|exists:shops,id',
            'basketDate' => 'required|date',
            'basketTotal' => 'required|numeric',
            'basketReceiptId' => 'required|string',
            'basketImage' => 'image',
        ]);
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
        $this->validate([
            'basketId' => 'required|integer|exists:baskets,id',
            'basketShop' => 'required|integer|exists:shops,id',
            'basketDate' => 'required|date',
            'basketTotal' => 'required|numeric',
            'basketReceiptId' => 'required|string',
        ]);
        if ($this->basketImage) {
            $this->basketImageURL = $this->basketImage->store('receipts', 'public');
        }
        Basket::where('id', $this->basketId)->where('user_id', auth()->user()->id)->update([
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
