<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;
use Livewire\WithFileUploads;

class Filepicker extends Component
{
    use WithFileUploads;

    public $file;

    public function render()
    {
        return view('livewire.component.filepicker');
    }
}
