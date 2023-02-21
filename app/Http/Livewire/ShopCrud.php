<?php

namespace App\Http\Livewire;

use Illuminate\Validation\ValidationException;

use App\Models\Address;
use App\Models\Company;
use App\Models\Shop;

class ShopCrud extends CrudPage
{
    public const PANEL_NAME = 'shopPanel';
    public $templateName = 'livewire.shop-crud';

    public $shopName = '';
    public $shopAddress = '';
    public $shopCompany = '';

    protected $listeners = [
        'shop.create' => 'saveNew',
        'shop.update' => 'update',
        'shop.delete' => 'delete',
        'action.back' => 'clearAction',
    ];

    public function delete($modelId)
    {
        $shop = Shop::where('id', $modelId)
            ->withCount('baskets')
            ->first();
        if ($shop != null && $shop->baskets_count == 0) {
            $shop->delete();
        }
        parent::clearAction();
    }

    public function getTemplateParameters()
    {
        return [
            'shops' =>  Shop::withCount('baskets')->with(['company', 'address'])->get(),
            'addresses' =>  Address::all(),
            'companies' =>  Company::all(),
            'panelShop' => [
                'shopName' => $this->shopName,
                'shopAddress' => $this->shopAddress,
                'shopCompany' => $this->shopCompany,
                'id' => $this->modelId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
            ],
        ];
    }

    public function initialize()
    {
        switch ($this->action) {
        case parent::ACTION_CREATE:
            $this->modelId = '';
            $this->shopName = '';
            $this->shopAddress = '';
            $this->shopCompany = '';
            $this->createdAt = '';
            $this->updatedAt = '';
            break;
        case parent::ACTION_READ:
        case parent::ACTION_UPDATE:
        case parent::ACTION_DELETE:
            $shop = Shop::find($this->modelId);
            $this->shopName = $shop->name;
            $this->shopAddress = $shop->address_id;
            $this->shopCompany = $shop->company_id;
            $this->createdAt = $shop->created_at;
            $this->updatedAt = $shop->updated_at;
            break;
        }
        if ($this->action == '') {
            $this->modelId = '';
            $this->emit('panel.close');
            return;
        }
        $this->emit('panel.update', self::PANEL_NAME, [
            'action' => $this->action,
            'shop' => [
                'shopName' => $this->shopName,
                'shopAddress' => $this->shopAddress,
                'shopCompany' => $this->shopCompany,
                'id' => $this->modelId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
            ],
            'addresses' => Address::all(),
            'companies' => Company::all(),
        ]);
        $this->emit('panel.open', self::PANEL_NAME);
    }

    public function saveNew(array $model)
    {
        $this->updateModelParams($model);
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
        $this->modelId = $shop->id;
        $this->setAction(parent::ACTION_UPDATE);
    }

    public function update(array $model)
    {
        $this->updateModelParams($model);
        $this->validate([
            'modelId' => 'required|integer|exists:shops,id',
            'shopName' => 'required|string',
            'shopCompany' => 'required|integer|exists:companies,id',
            'shopAddress' => 'required|integer|exists:addresses,id',
        ]);
        Shop::where('id', $this->modelId)->update([
            'name' => $this->shopName,
            'address_id' => $this->shopAddress,
            'company_id' => $this->shopCompany,
        ]);
        $this->setAction(parent::ACTION_UPDATE);
    }

    private function updateModelParams(array $model)
    {
        if (array_key_exists('shopName', $model)) {
            $this->shopName = $model['shopName'];
        }
        if (array_key_exists('shopAddress', $model)) {
            $this->shopAddress = $model['shopAddress'];
        }
        if (array_key_exists('shopCompany', $model)) {
            $this->shopCompany = $model['shopCompany'];
        }
    }
}
