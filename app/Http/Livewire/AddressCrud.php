<?php

namespace App\Http\Livewire;

use Illuminate\Validation\ValidationException;

use App\Models\Address;

class AddressCrud extends OffcanvasPage
{
    public $templateName = 'livewire.address-crud';

    public $addressRaw = '';

    protected $listeners = ['offcanvasClose'];

    public function load($id)
    {
        $this->modelId = $id;
        $this->action = parent::ACTION_UPDATE;
        $address = Address::find($this->modelId);
        $this->addressRaw = $address->raw;
        $this->createdAt = $address->created_at;
        $this->updatedAt = $address->updated_at;
    }

    public function delete($id)
    {
        $address = Address::find($id);
        if ($address != null && $address->companies->count() == 0 && $address->shops->count() == 0) {
            $address->delete();
        }
    }

    public function getTemplateParameters()
    {
        return [
            'addresses' =>  Address::all()
        ];
    }

    public function initialize()
    {
            $this->modelId = '';
            $this->addressRaw = '';
            $this->createdAt = '';
            $this->updatedAt = '';
    }

    public function saveNew()
    {
        try {
            $this->validate([
                'addressRaw' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            $messages = $e->validator->getMessageBag();
            $this->dispatchBrowserEvent('model.validation', ['type' => 'new', 'model' => 'Address', 'messages' => $messages]);
            return;
        }
        $address = Address::firstOrCreate([
            'raw' => $this->addressRaw,
        ]);
        return redirect()->route('address', ['action' => 'update', 'id' => $address->id]);
    }
    public function update()
    {
        $this->validate([
            'addressRaw' => 'required|string',
            'modelId' => 'required|integer',
        ]);
        Address::where('id', $this->modelId)->update([
            'raw' => $this->addressRaw,
        ]);
        return redirect()->route('address', ['action' => 'update', 'id' => $this->modelId]);
    }
}
