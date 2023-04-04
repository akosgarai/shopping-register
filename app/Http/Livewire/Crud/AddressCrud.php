<?php

namespace App\Http\Livewire\Crud;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

use App\Models\Address;

class AddressCrud extends CrudPage
{
    public const PANEL_NAME = 'addressPanel';
    public const ORDERABLE_COLUMNS = ['id', 'raw', 'created_at', 'updated_at'];
    public $templateName = 'livewire.crud.address-crud';

    public $addressRaw = '';

    protected $listeners = [
        'address.create' => 'saveNew',
        'address.update' => 'update',
        'address.delete' => 'delete',
        'action.back' => 'clearAction',
        'search' => 'search',
    ];

    public function delete($modelId)
    {
        $address = Address::where('id', $modelId)
            ->withCount('companies')
            ->withCount('shops')
            ->first();
        if ($address != null && $address->companies_count == 0 && $address->shops_count == 0) {
            $address->delete();
        }
        parent::clearAction();
    }

    public function getTemplateParameters()
    {
        if ($this->modelId != '') {
            $this->viewData = $this->getAddress()->toArray();
        }
        return [
            'addresses' => $this->addressList(),
            'viewData' => $this->viewData,
            'panelAddress' => [
                'raw' => $this->addressRaw,
                'id' => $this->modelId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
            ]
        ];
    }

    public function initialize()
    {
        switch ($this->action) {
            case parent::ACTION_CREATE:
                $this->addressRaw = '';
                $this->createdAt = '';
                $this->updatedAt = '';
                break;
            case parent::ACTION_READ:
            case parent::ACTION_UPDATE:
            case parent::ACTION_DELETE:
                $address = $this->getAddress();
                $this->addressRaw = $address->raw;
                $this->createdAt = $address->created_at;
                $this->updatedAt = $address->updated_at;
                $this->viewData = $address->toArray();
                break;
        }
        if ($this->action == '') {
            $this->modelId = '';
            $this->emit('panel.close');
            return;
        }
        // We have a livewire component in the panel, so instead of calling the 'panel.update'
        // event, we have to call the 'crudaction.update' event.
        $this->emit('crudaction.update', [
            'action' => $this->action,
            'viewData' => $this->viewData,
            'address' => [
                'raw' => $this->addressRaw,
                'id' => $this->modelId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
            ]
        ]);
        $this->emit('panel.open', self::PANEL_NAME);
    }

    public function saveNew(array $model)
    {
        $this->updateModelParams($model);
        $this->validate([
            'addressRaw' => 'required|string|max:255',
        ]);
        $address = (new Address())->firstOrCreate([
            'raw' => $this->addressRaw,
        ]);
        $this->modelId = $address->id;
        $this->setAction(parent::ACTION_UPDATE);
    }
    public function update(array $model)
    {
        $this->updateModelParams($model);
        $this->validate([
            'addressRaw' => 'required|string|max:255',
            'modelId' => 'required|integer|exists:addresses,id',
        ]);
        Address::where('id', $this->modelId)->update([
            'raw' => $this->addressRaw,
        ]);
        $this->setAction(parent::ACTION_UPDATE);
    }

    private function updateModelParams(array $model)
    {
        if (array_key_exists('raw', $model)) {
            $this->addressRaw = $model['raw'];
        }
    }

    private function getAddress()
    {
        return Address::where('id', $this->modelId)
            ->first();
    }

    private function addressList(): LengthAwarePaginator
    {
        $query = Address::withCount('companies')->withCount('shops');
        if ($this->search != '') {
            $query = $query->where('raw', 'like', '%' . $this->search . '%');
        }
        return $this->getPaginatedData($query);
    }
}
