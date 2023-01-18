<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Address;

class AddressCrud extends Component
{
    public $action = '';

    public $addressRaw = '';
    public $addressId = '';
    public $createdAt = '';
    public $updatedAt = '';

    protected $queryString = [
        'action' => ['except' => ''],
        'addressId' => ['except' => '', 'as' => 'id'],
    ];

    public function mount()
    {
        $this->action = request()->query('action', '');
        $id = request()->query('id', '');
        if ($id != '') {
            $this->loadAddress($id);
        }
    }

    public function loadAddress($id)
    {
        $this->addressId = $id;
        $this->action = 'update';
        $address = Address::find($this->addressId);
        $this->addressRaw = $address->raw;
        $this->createdAt = $address->created_at;
        $this->updatedAt = $address->updated_at;
    }

    public function deleteAddress($id)
    {
        $address = Address::find($id);
        if ($address != null && $address->companies->count() == 0 && $address->shops->count() == 0) {
            $address->delete();
        }
    }

    public function render()
    {
        return view('livewire.address-crud', [ 'addresses' =>  Address::all() ])
            ->extends('layouts.app');
    }

    public function setAction($action)
    {
        $this->action = $action;
        if ($action != 'update') {
            $this->addressId = '';
            $this->addressRaw = '';
            $this->createdAt = '';
            $this->updatedAt = '';
        }
    }

    public function saveNewAddress()
    {
        $this->validate([
            'addressRaw' => 'required|string',
        ]);
        $address = Address::firstOrCreate([
            'raw' => $this->addressRaw,
        ]);
        return redirect()->route('address', ['action' => 'update', 'id' => $address->id]);
    }
    public function updateAddress()
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
