<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Item;

class ItemCrud extends Component
{
    public $action = '';
    public $itemId = '';
    public $itemName = '';
    public $createdAt = '';
    public $updatedAt = '';

    protected $queryString = [
        'action' => ['except' => ''],
        'itemId' => ['except' => '', 'as' => 'id'],
    ];

    public function mount()
    {
        $this->action = request()->query('action', '');
        $id = request()->query('id', '');
        if ($id != '') {
            $this->loadItem($id);
        }
    }

    public function loadItem($id)
    {
        $this->itemId = $id;
        $this->action = 'update';
        $item = Item::find($this->itemId);
        $this->itemName = $item->name;
        $this->createdAt = $item->created_at;
        $this->updatedAt = $item->updated_at;
    }

    public function render()
    {
        return view('livewire.item-crud', [ 'items' =>  Item::all() ])
            ->extends('layouts.app');
    }

    public function setAction($action)
    {
        $this->action = $action;
        if ($action != 'update') {
            $this->itemId = '';
            $this->itemName = '';
            $this->createdAt = '';
            $this->updatedAt = '';
        }
    }

    public function saveNewItem()
    {
        $this->validate([
            'itemName' => 'required|string',
        ]);
        $item = Item::firstOrCreate([
            'name' => $this->itemName,
        ]);
        return redirect()->route('item', ['action' => 'update', 'id' => $item->id]);
    }

    public function updateItem()
    {
        $this->validate([
            'itemName' => 'required|string',
            'itemId' => 'required|integer',
        ]);
        Item::where('id', $this->itemId)->update([
            'name' => $this->itemName,
        ]);
        return redirect()->route('item', ['action' => 'update', 'id' => $this->itemId]);
    }

    public function deleteItem($id)
    {
        $item = Item::find($id);
        if ($item != null && $item->basketItems->count() == 0) {
            $item->delete();
        }
    }
}
