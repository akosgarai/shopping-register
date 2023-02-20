<?php

namespace App\Http\Livewire;

use Illuminate\Validation\ValidationException;

use App\Models\Item;

class ItemCrud extends CrudPage
{
    public const PANEL_NAME = 'itemPanel';
    public $templateName = 'livewire.item-crud';

    public $itemName = '';

    protected $listeners = [
        'item.create' => 'saveNew',
        'item.update' => 'update',
        'item.delete' => 'delete',
        'action.back' => 'clearAction',
    ];

    public function delete($modelId)
    {
        $item = Item::where('id', $modelId)
            ->withCount('basketItems')
            ->first();
        if ($item != null && $item->basketItems_count == 0) {
            $item->delete();
        }
        parent::clearAction();
    }

    public function getTemplateParameters()
    {
        return [
            'items' =>  Item::withCount('basketItems')->get(),
        ];
    }

    public function initialize()
    {
        switch ($this->action) {
            case parent::ACTION_CREATE:
                $this->itemName = '';
                $this->createdAt = '';
                $this->updatedAt = '';
                break;
            case parent::ACTION_READ:
            case parent::ACTION_UPDATE:
            case parent::ACTION_DELETE:
                $item = Item::find($this->modelId);
                $this->itemName = $item->name;
                $this->createdAt = $item->created_at;
                $this->updatedAt = $item->updated_at;
                break;
        }
        if ($this->action == '') {
            $this->modelId = '';
            $this->emit('panel.close');
            return;
        }
        $this->emit('panel.update', self::PANEL_NAME, [
            'action' => $this->action,
            'item' => [
                'itemName' => $this->itemName,
                'id' => $this->modelId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
            ]
        ]);
        $this->emit('panel.open', self::PANEL_NAME);
    }

    public function saveNew(array $model)
    {
        if (array_key_exists('itemName', $model)) {
            $this->itemName = $model['itemName'];
        }
        $this->validate([
            'itemName' => 'required|string',
        ]);
        $item = Item::firstOrCreate([
            'name' => $this->itemName,
        ]);
        $this->modelId = $item->id;
        $this->setAction(parent::ACTION_UPDATE);
    }

    public function update(array $model)
    {
        if (array_key_exists('itemName', $model)) {
            $this->itemName = $model['itemName'];
        }
        $this->validate([
            'itemName' => 'required|string',
            'modelId' => 'required|integer',
        ]);
        Item::where('id', $this->modelId)->update([
            'name' => $this->itemName,
        ]);
        $this->setAction(parent::ACTION_UPDATE);
    }
}
