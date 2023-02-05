<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class ImageManipulator extends Component
{
    public $image = null;

    public function render()
    {
        return view('livewire.component.image-manipulator');
    }
}
