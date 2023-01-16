<?php

namespace App\Http\Livewire\Receipt;

use Livewire\Component;

class Details extends Component
{
    public $basket;

    public function render()
    {
        return view('livewire.receipt.details');
    }
}
