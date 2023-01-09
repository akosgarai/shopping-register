<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class Imageditor extends Component
{
    public $editMode = false;

    public function render()
    {
        return view('livewire.component.imageditor');
    }

    public function broadcastCrop()
    {
        $this->editMode = 'crop';
        $this->dispatchBrowserEvent('editor.crop');
    }

    public function broadcastApply()
    {
        $this->dispatchBrowserEvent('editor.apply.'.$this->editMode);
        $this->editMode = false;
    }

    public function broadcastCancel()
    {
        $this->dispatchBrowserEvent('editor.cancel.'.$this->editMode);
        $this->editMode = false;
    }
}
