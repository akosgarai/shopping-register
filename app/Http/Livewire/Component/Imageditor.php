<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class Imageditor extends Component
{
    const MODE_CROP = 'crop';

    const EVENT_CROP = 'editor.crop';
    const EVENT_CANCEL = 'editor.cancel';
    const EVENT_APPLY = 'editor.apply.';
    const EVENT_EDIT_COMPLETE = 'editor.complete';
    const EVENT_FILTER = 'editor.filter';

    const FILTER_GRAYSCALE = 'Grayscale';
    const FILTER_INVERT = 'Invert';
    const FILTER_SEPIA = 'Sepia';
    const FILTER_SHARPEN = 'Sharpen';
    const FILTER_EMBOSS = 'Emboss';

    public $editMode = false;

    public $filters = [
        self::FILTER_GRAYSCALE => false,
        self::FILTER_INVERT => false,
        self::FILTER_SEPIA => false,
        self::FILTER_SHARPEN => false,
        self::FILTER_EMBOSS => false,
    ];

    public function render()
    {
        return view('livewire.component.imageditor');
    }

    public function broadcastCrop()
    {
        $this->editMode = self::MODE_CROP;
        $this->dispatchBrowserEvent(self::EVENT_CROP);
    }

    public function broadcastApply()
    {
        $this->dispatchBrowserEvent(self::EVENT_APPLY.$this->editMode);
        $this->editMode = false;
    }

    public function broadcastCancel()
    {
        $this->dispatchBrowserEvent(self::EVENT_CANCEL, ['action' => $this->editMode]);
        $this->editMode = false;
    }
    public function broadcastEditComplete()
    {
        $this->editMode = false;
        $this->dispatchBrowserEvent(self::EVENT_EDIT_COMPLETE);
    }
    public function broadcastFilter($filter)
    {
        $this->filters[$filter] = !$this->filters[$filter];
        $this->dispatchBrowserEvent(self::EVENT_FILTER, ['filter' => $filter, 'value' => $this->filters[$filter]]);
    }
}
