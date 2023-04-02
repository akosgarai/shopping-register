<?php

namespace App\Http\Livewire\Crud;

use Illuminate\Validation\ValidationException;

use App\Models\Address;
use App\Models\Company;
use App\Models\Shop;

class ShopCrud extends CrudPage
{
    public const PANEL_NAME = 'shopPanel';
    public const ORDERABLE_COLUMNS = ['id', 'name', 'created_at', 'updated_at'];
    public $templateName = 'livewire.crud.shop-crud';

    public $name = '';
    public $address = '';
    public $company = '';

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
        if ($this->modelId != '') {
            $this->viewData = $this->getShop()->toArray();
        }
        return [
            'shops' => $this->shopList(),
            'addresses' =>  (new Address())->all(),
            'companies' =>  (new Company())->all(),
            'viewData' => $this->viewData,
            'panelShop' => [
                'name' => $this->name,
                'address' => $this->address,
                'company' => $this->company,
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
            $this->name = '';
            $this->address = '';
            $this->company = '';
            $this->createdAt = '';
            $this->updatedAt = '';
            break;
        case parent::ACTION_READ:
        case parent::ACTION_UPDATE:
        case parent::ACTION_DELETE:
            $shop = $this->getShop();
            $this->name = $shop->name;
            $this->address = $shop->address_id;
            $this->company = $shop->company_id;
            $this->createdAt = $shop->created_at;
            $this->updatedAt = $shop->updated_at;
            $this->viewData = $shop->toArray();
            break;
        }
        if ($this->action == '') {
            $this->modelId = '';
            $this->emit('panel.close');
            return;
        }
        $this->emit('crudaction.update', [
            'action' => $this->action,
            'viewData' => $this->viewData,
            'shop' => [
                'name' => $this->name,
                'address' => $this->address,
                'company' => $this->company,
                'id' => $this->modelId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
            ],
            'addresses' => (new Address())->all(),
            'companies' => (new Company())->all(),
        ]);
        $this->emit('panel.open', self::PANEL_NAME);
    }

    public function saveNew(array $model)
    {
        $this->updateModelParams($model);
        $this->validate([
            'name' => 'required|string',
            'company' => 'required|integer|exists:companies,id',
            'address' => 'required|integer|exists:addresses,id',
        ]);
        $shop = (new Shop())->firstOrCreate([
            'name' => $this->name,
            'address_id' => $this->address,
            'company_id' => $this->company,
        ]);
        $this->modelId = $shop->id;
        $this->setAction(parent::ACTION_UPDATE);
    }

    public function update(array $model)
    {
        $this->updateModelParams($model);
        $this->validate([
            'modelId' => 'required|integer|exists:shops,id',
            'name' => 'required|string',
            'company' => 'required|integer|exists:companies,id',
            'address' => 'required|integer|exists:addresses,id',
        ]);
        Shop::where('id', $this->modelId)->update([
            'name' => $this->name,
            'address_id' => $this->address,
            'company_id' => $this->company,
        ]);
        $this->setAction(parent::ACTION_UPDATE);
    }

    private function updateModelParams(array $model)
    {
        if (array_key_exists('name', $model)) {
            $this->name = $model['name'];
        }
        if (array_key_exists('address', $model)) {
            $this->address = $model['address'];
        }
        if (array_key_exists('company', $model)) {
            $this->company = $model['company'];
        }
    }

    private function getShop()
    {
        return Shop::where('id', $this->modelId)
            ->with(['company', 'address'])
            ->first();
    }

    private function shopList()
    {
        return Shop::withCount('baskets')->with(['company', 'address'])
            ->orderBy($this->orderColumn, $this->orderDirection)
            ->paginate(parent::ITEM_LIMIT);
    }
}
