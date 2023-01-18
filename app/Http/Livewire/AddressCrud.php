<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Address;

class AddressCrud extends Component
{
    public function render()
    {
        return view('livewire.address-crud', [ 'addresses' =>  Address::all() ])
            ->extends('layouts.app');
    }
}
