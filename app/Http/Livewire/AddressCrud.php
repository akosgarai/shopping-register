<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Address;

class AddressCrud extends Component
{
    public $action = '';

    public $addressRaw = '';

    protected $queryString = [
        'action' => ['except' => ''],
    ];

    public function render()
    {
        return view('livewire.address-crud', [ 'addresses' =>  Address::all() ])
            ->extends('layouts.app');
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function saveNewAddress()
    {
        $validated = $this->validate([
            'addressRaw' => 'required|string',
        ]);
        Address::create([
            'raw' => $this->addressRaw,
        ]);
        $this->addressRaw = '';
    }
}
