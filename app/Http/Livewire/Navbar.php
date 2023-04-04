<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Navbar extends Component
{
    public $search = '';

    public function render()
    {
        return view('livewire.navbar');
    }

    public function search()
    {
        $this->emit('search', $this->search);
    }
}
