<?php

namespace App\Http\Livewire;

use App\Models\Address;

class AddressCrud extends OffcanvasPage
{
    public $templateName = 'livewire.address-crud';

    public $addressRaw = '';
    public $addressId = '';
    public $createdAt = '';
    public $updatedAt = '';

    protected $queryString = [
        'action' => ['except' => ''],
        'addressId' => ['except' => '', 'as' => 'id'],
    ];

    public function load($id)
    {
        $this->addressId = $id;
        $this->action = 'update';
        $address = Address::find($this->addressId);
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
            $this->addressId = '';
            $this->addressRaw = '';
            $this->createdAt = '';
            $this->updatedAt = '';
    }

    public function saveNew()
    {
        $this->validate([
            'addressRaw' => 'required|string',
        ]);
        $address = Address::firstOrCreate([
            'raw' => $this->addressRaw,
        ]);
        return redirect()->route('address', ['action' => 'update', 'id' => $address->id]);
    }
    public function update()
    {
        $this->validate([
            'addressRaw' => 'required|string',
            'addressId' => 'required|integer',
        ]);
        Address::where('id', $this->addressId)->update([
            'raw' => $this->addressRaw,
        ]);
        return redirect()->route('address', ['action' => 'update', 'id' => $this->addressId]);
    }
}
