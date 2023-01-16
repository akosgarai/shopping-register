<?php

namespace App\Http\Livewire\Receipt;

use Livewire\Component;

class Card extends Component
{

    public $basket;

    public function render()
    {
        return view('livewire.receipt.card');
    }
}
