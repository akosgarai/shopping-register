<?php

namespace App\Http\Livewire\Component\Scan;

use Livewire\Component;

class Parser extends Component
{
    public $parsers = [];

    public function render()
    {
        return view('livewire.component.scan.parser');
    }
}
