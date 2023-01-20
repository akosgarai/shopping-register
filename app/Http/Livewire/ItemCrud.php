<?php

namespace App\Http\Livewire;

use Illuminate\Validation\ValidationException;

use App\Models\Item;

class ItemCrud extends OffcanvasPage
{
    public $templateName = 'livewire.item-crud';

    public $itemName = '';

    protected $listeners = ['offcanvasClose'];

    public function load($id)
    {
        $this->modelId = $id;
        $this->action = parent::ACTION_UPDATE;
        $item = Item::find($this->modelId);
        $this->itemName = $item->name;
        $this->createdAt = $item->created_at;
        $this->updatedAt = $item->updated_at;
    }

    public function getTemplateParameters()
    {
        return [
            'items' =>  Item::all()
        ];
    }

    public function initialize()
    {
            $this->modelId = '';
            $this->itemName = '';
            $this->createdAt = '';
            $this->updatedAt = '';
    }

    public function saveNew()
    {
        try {
            $this->validate([
                'itemName' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            $messages = $e->validator->getMessageBag();
            $this->dispatchBrowserEvent('model.validation', ['type' => 'new', 'model' => 'Item', 'messages' => $messages]);
            return;
        }
        $item = Item::firstOrCreate([
            'name' => $this->itemName,
        ]);
        return redirect()->route('item', ['action' => 'update', 'id' => $item->id]);
    }

    public function update()
    {
        try {
            $this->validate([
                'itemName' => 'required|string',
                'itemId' => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            $messages = $e->validator->getMessageBag();
            $this->dispatchBrowserEvent('model.validation', ['type' => 'update', 'model' => 'Item', 'messages' => $messages]);
            return;
        }
        Item::where('id', $this->modelId)->update([
            'name' => $this->itemName,
        ]);
        return redirect()->route('item', ['action' => 'update', 'id' => $this->modelId]);
    }

    public function delete($id)
    {
        $item = Item::find($id);
        if ($item != null && $item->basketItems->count() == 0) {
            $item->delete();
        }
    }
}
