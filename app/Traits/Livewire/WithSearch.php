<?php

namespace App\Traits\Livewire;

trait WithSearch
{
    // The search string.
    public $search = '';

    // This method is called when the search string is changed.
    // The search string is emitted to the component that can handle it.
    public function search()
    {
        $this->emit('search', $this->search);
    }
}
