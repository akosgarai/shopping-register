<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Address;
use App\Models\Company;
use App\Models\Shop;

class ShopCrud extends Component
{
    public $action = '';
    public $shopId = '';
    public $shopName = '';
    public $shopAddress = '';
    public $shopCompany = '';
    public $createdAt = '';
    public $updatedAt = '';

    protected $queryString = [
        'action' => ['except' => ''],
        'shopId' => ['except' => '', 'as' => 'id'],
    ];

    public function mount()
    {
        $this->action = request()->query('action', '');
        $id = request()->query('id', '');
        if ($id != '') {
            $this->loadShop($id);
        }
    }

    public function loadShop($id)
    {
        $this->shopId = $id;
        $this->action = 'update';
        $shop = Shop::find($this->shopId);
        $this->shopName = $shop->name;
        $this->shopAddress = $shop->address_id;
        $this->shopCompany = $shop->company_id;
        $this->createdAt = $shop->created_at;
        $this->updatedAt = $shop->updated_at;
    }

    public function render()
    {
        return view('livewire.shop-crud', [ 'companies' =>  Company::all(), 'addresses' =>  Address::all(), 'shops' =>  Shop::all() ])
            ->extends('layouts.app');
    }

    public function setAction($action)
    {
        $this->action = $action;
        if ($action != 'update') {
            $this->shopId = '';
            $this->shopName = '';
            $this->shopCompany = '';
            $this->shopAddress = '';
            $this->createdAt = '';
            $this->updatedAt = '';
        }
    }

    public function saveNewShop()
    {
        $this->validate([
            'shopName' => 'required|string',
            'shopCompany' => 'required|integer|exists:companies,id',
            'shopAddress' => 'required|integer|exists:addresses,id',
        ]);
        $shop = Shop::firstOrCreate([
            'name' => $this->shopName,
            'address_id' => $this->shopAddress,
            'company_id' => $this->shopCompany,
        ]);
        return redirect()->route('shop', ['action' => 'update', 'id' => $shop->id]);
    }

    public function updateShop()
    {
        $this->validate([
            'shopId' => 'required|integer|exists:shops,id',
            'shopName' => 'required|string',
            'shopCompany' => 'required|integer|exists:companies,id',
            'shopAddress' => 'required|integer|exists:addresses,id',
        ]);
        Shop::where('id', $this->shopId)->update([
            'name' => $this->shopName,
            'address_id' => $this->shopAddress,
            'company_id' => $this->shopCompany,
        ]);
        return redirect()->route('shop', ['action' => 'update', 'id' => $this->shopId]);
    }

    public function deleteShop($id)
    {
        $shop = Shop::find($id);
        if ($shop != null && $shop->baskets->count() == 0) {
            $shop->delete();
        }
    }
}
