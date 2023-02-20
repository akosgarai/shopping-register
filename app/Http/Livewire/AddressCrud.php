<?php

namespace App\Http\Livewire;

use Illuminate\Validation\ValidationException;

use App\Models\Address;

class AddressCrud extends CrudPage
{
    public const PANEL_NAME = 'addressPanel';
    public $templateName = 'livewire.address-crud';

    public $addressRaw = '';

    protected $listeners = [
        'address.create' => 'saveNew',
        'address.update' => 'update',
        'address.delete' => 'delete',
        'action.back' => 'clearAction',
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
        return [
            'addresses' =>  Address::all()
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
                $address = Address::find($this->modelId);
                $this->addressRaw = $address->raw;
                $this->createdAt = $address->created_at;
                $this->updatedAt = $address->updated_at;
                break;
        }
        if ($this->action == '') {
            $this->modelId = '';
            $this->emit('panel.close');
            return;
        }
        $this->emit('panel.update', self::PANEL_NAME, [
            'action' => $this->action,
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
        if (array_key_exists('raw', $model)) {
            $this->addressRaw = $model['raw'];
        }
        $this->validate([
            'addressRaw' => 'required|string',
        ]);
        $address = Address::firstOrCreate([
            'raw' => $this->addressRaw,
        ]);
        $this->modelId = $address->id;
        $this->setAction(parent::ACTION_UPDATE);
    }
    public function update(array $model)
    {
        if (array_key_exists('raw', $model)) {
            $this->addressRaw = $model['raw'];
        }
        $this->validate([
            'addressRaw' => 'required|string',
            'modelId' => 'required|integer',
        ]);
        Address::where('id', $this->modelId)->update([
            'raw' => $this->addressRaw,
        ]);
        $this->setAction(parent::ACTION_UPDATE);
    }
}
