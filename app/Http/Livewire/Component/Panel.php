<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class Panel extends Component
{
    public $open = false;
    public $position = 'left';
    public $panelName = '';
    public $panelTitle = '';
    public $contentTemplate = '';
    public $contentParameters = [];

    protected $listeners = ['panel.open' => 'openPanel', 'panel.close' => 'closePanel', 'panel.update' => 'updatePanel'];

    public function render()
    {
        return view('livewire.component.panel');
    }

    public function openPanel($name)
    {
        if ($name == $this->panelName) {
            $this->open = true;
        }
    }

    // If the panel is open, close it and send a close event to the parent component.
    public function closePanel($name = '')
    {
        if ($this->open && ($name == '' || $name == $this->panelName)) {
            $this->open = false;
            $this->emitUp('panel.close', $this->panelName);
        }
    }

    public function updatePanel($name, $contentParameters)
    {
        if ($name == $this->panelName) {
            $this->contentParameters = $contentParameters;
        }
    }
}
