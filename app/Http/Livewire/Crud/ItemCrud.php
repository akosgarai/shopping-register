<?php

namespace App\Http\Livewire\Crud;

use Illuminate\Validation\ValidationException;

use App\Models\Item;

class ItemCrud extends CrudPage
{
    public const PANEL_NAME = 'itemPanel';
    const ORDERABLE_COLUMNS = ['id', 'name', 'created_at', 'updated_at'];
    public $templateName = 'livewire.crud.item-crud';

    public $name = '';

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
        if ($item != null && $item->basket_items_count == 0) {
            $item->delete();
        }
        parent::clearAction();
    }

    public function getTemplateParameters()
    {
        if ($this->modelId != '') {
            $this->viewData = $this->getItem()->toArray();
        }
        return [
            'items' =>  $this->itemList(),
            'viewData' => $this->viewData,
            'panelItem' => [
                'name' => $this->name,
                'id' => $this->modelId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
            ]
        ];
    }

    public function initialize()
    {
        switch ($this->action) {
            case parent::ACTION_CREATE:
                $this->name = '';
                $this->createdAt = '';
                $this->updatedAt = '';
                break;
            case parent::ACTION_READ:
            case parent::ACTION_UPDATE:
            case parent::ACTION_DELETE:
                $item = $this->getItem();
                $this->name = $item->name;
                $this->createdAt = $item->created_at;
                $this->updatedAt = $item->updated_at;
                $this->viewData = $item->toArray();
                break;
        }
        if ($this->action == '') {
            $this->modelId = '';
            $this->emit('panel.close');
            return;
        }
        $this->emit('crudaction.update', [
            'action' => $this->action,
            'viewData' => $this->viewData,
            'item' => [
                'name' => $this->name,
                'id' => $this->modelId,
                'createdAt' => $this->createdAt,
                'updatedAt' => $this->updatedAt,
            ]
        ]);
        $this->emit('panel.open', self::PANEL_NAME);
    }

    public function saveNew(array $model)
    {
        if (array_key_exists('name', $model)) {
            $this->name = $model['name'];
        }
        $this->validate([
            'name' => 'required|string|max:255',
        ]);
        $item = (new Item())->firstOrCreate([
            'name' => $this->name,
        ]);
        $this->modelId = $item->id;
        $this->setAction(parent::ACTION_UPDATE);
    }

    public function update(array $model)
    {
        if (array_key_exists('name', $model)) {
            $this->name = $model['name'];
        }
        $this->validate([
            'name' => 'required|string',
            'modelId' => 'required|integer',
        ]);
        Item::where('id', $this->modelId)->update([
            'name' => $this->name,
        ]);
        $this->setAction(parent::ACTION_UPDATE);
    }

    private function getItem()
    {
        return Item::where('id', $this->modelId)
            ->first();
    }

    private function itemList()
    {
        return Item::withCount('basketItems')
            ->orderBy($this->orderColumn, $this->orderDirection)
            ->paginate(parent::ITEM_LIMIT);
    }
}
