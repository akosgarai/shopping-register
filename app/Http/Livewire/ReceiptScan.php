<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ReceiptScan extends Component
{
    const ACTION_PICK = 'pick';
    const ACTION_EDIT = 'edit';
    const ACTION_PARSE = 'parse';
    const ACTION_BASKET = 'basket';

    public $action = '';

    // The query string parameters.
    protected $queryString = [
        'action' => ['except' => ''],
    ];

    // Initialize the component based on the query string parameters.
    public function mount()
    {
        $this->action = request()->query('action', '');
    }

    public function render()
    {
        return view('livewire.receipt-scan')->extends('layouts.app');
    }
}
