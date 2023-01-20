<?php

namespace App\Http\Livewire;

use Illuminate\Validation\ValidationException;

use App\Models\Address;
use App\Models\Company;
use App\Models\Shop;

class ShopCrud extends OffcanvasPage
{
    public $templateName = 'livewire.shop-crud';

    public $shopName = '';
    public $shopAddress = '';
    public $shopCompany = '';

    protected $listeners = ['offcanvasClose'];

    public function load($id)
    {
        $this->modelId = $id;
        $this->action = parent::ACTION_UPDATE;
        $shop = Shop::find($this->modelId);
        $this->shopName = $shop->name;
        $this->shopAddress = $shop->address_id;
        $this->shopCompany = $shop->company_id;
        $this->createdAt = $shop->created_at;
        $this->updatedAt = $shop->updated_at;
    }

    public function getTemplateParameters()
    {
        return [
            'shops' =>  Shop::all(),
            'addresses' =>  Address::all(),
            'companies' =>  Company::all()
        ];
    }

    public function initialize()
    {
            $this->modelId = '';
            $this->shopName = '';
            $this->shopAddress = '';
            $this->shopCompany = '';
            $this->createdAt = '';
            $this->updatedAt = '';
    }

    public function saveNew()
    {
        try {
            $this->validate([
                'shopName' => 'required|string',
                'shopCompany' => 'required|integer|exists:companies,id',
                'shopAddress' => 'required|integer|exists:addresses,id',
            ]);
        } catch (ValidationException $e) {
            $messages = $e->validator->getMessageBag();
            $this->dispatchBrowserEvent('model.validation', ['type' => 'new', 'model' => 'Shop', 'messages' => $messages]);
            return;
        }
        $shop = Shop::firstOrCreate([
            'name' => $this->shopName,
            'address_id' => $this->shopAddress,
            'company_id' => $this->shopCompany,
        ]);
        return redirect()->route('shop', ['action' => 'update', 'id' => $shop->id]);
    }

    public function update()
    {
        try {
            $this->validate([
                'modelId' => 'required|integer|exists:shops,id',
                'shopName' => 'required|string',
                'shopCompany' => 'required|integer|exists:companies,id',
                'shopAddress' => 'required|integer|exists:addresses,id',
            ]);
        } catch (ValidationException $e) {
            $messages = $e->validator->getMessageBag();
            $this->dispatchBrowserEvent('model.validation', ['type' => 'update', 'model' => 'Shop', 'messages' => $messages]);
            return;
        }
        Shop::where('id', $this->modelId)->update([
            'name' => $this->shopName,
            'address_id' => $this->shopAddress,
            'company_id' => $this->shopCompany,
        ]);
        return redirect()->route('shop', ['action' => 'update', 'id' => $this->modelId]);
    }

    public function delete($id)
    {
        $shop = Shop::find($id);
        if ($shop != null && $shop->baskets->count() == 0) {
            $shop->delete();
        }
    }
}
