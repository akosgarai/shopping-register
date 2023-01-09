<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;
use Livewire\WithFileUploads;

class Filepicker extends Component
{
    use WithFileUploads;

    public $file = null;

    public function render()
    {
        return view('livewire.component.filepicker');
    }

    public function broadcastImage()
    {
        $this->validate([
            'file' => 'required|image',
        ]);
        $this->dispatchBrowserEvent('imageUploaded', ['fileTmpUrl' => '/storage/'.$this->file->store('tmp', 'public')]);
    }
}
