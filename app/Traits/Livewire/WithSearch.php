<?php

namespace App\Traits\Livewire;

trait WithSearch
{
    // The search string.
    public $search = '';

    // This method is called when the search string is changed.
    // The search string is updated in the query string also and the page
    // is reset to update the list.
    public function search($search)
    {
        $this->search = $search;
        $this->resetPage();
    }
}
