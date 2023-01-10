<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class Imageditor extends Component
{
    public $editMode = false;

    public $filters = [
        'Grayscale' => false,
        'Invert' => false,
        'Sepia' => false,
        'Sharpen' => false,
        'Emboss' => false,
    ];

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
        $this->dispatchBrowserEvent('editor.cancel', ['action' => $this->editMode]);
        $this->editMode = false;
    }
    public function broadcastEditComplete()
    {
        $this->editMode = false;
        $this->dispatchBrowserEvent('editor.complete');
    }
    public function broadcastFilter($filter)
    {
        $this->filters[$filter] = !$this->filters[$filter];
        $this->dispatchBrowserEvent('editor.filter', ['filter' => $filter, 'value' => $this->filters[$filter]]);
    }
}
