<?php

namespace Tests\Feature\Livewire\Crud;

use App\Http\Livewire\Crud\ItemCrud;
use App\Models\BasketItem;
use App\Models\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ItemCrudTest extends TestCase
{
    use RefreshDatabase;

    public function testTheComponentCanRender()
    {
        Livewire::test(ItemCrud::class)->assertStatus(200);
    }

    public function testSetCreateAction()
    {
        $this->validateCreateAction($this->caseItems());
    }

    public function testLoadWithAction()
    {
        foreach (ItemCrud::ACTIONS as $action) {
            if ($action == ItemCrud::ACTION_CREATE) {
                continue;
            }
            $this->caseOpenToAction($action);
        }
    }

    public function testDelete()
    {
        $case = $this->caseItems();
        $items = $case->viewData('items');
        foreach ($items as $item) {
            $case = $this->validateDeletion($case, $item);
        }
    }

    public function testLoadForView()
    {
        $case = $this->caseItems();
        $items = $case->viewData('items');
        foreach ($items as $item) {
            $case->call('loadForView', $item->id)
                 ->assertSet('action', ItemCrud::ACTION_READ)
                 ->assertSet('modelId', $item->id)
                 ->assertSet('name', $item->name)
                 ->assertSet('createdAt', $item->created_at)
                 ->assertSet('updatedAt', $item->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', ItemCrud::PANEL_NAME);
        }
    }

    public function testLoadForDelete()
    {
        $case = $this->caseItems();
        $items = $case->viewData('items');
        foreach ($items as $item) {
            $case->call('loadForDelete', $item->id)
                 ->assertSet('action', ItemCrud::ACTION_DELETE)
                 ->assertSet('modelId', $item->id)
                 ->assertSet('name', $item->name)
                 ->assertSet('createdAt', $item->created_at)
                 ->assertSet('updatedAt', $item->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', ItemCrud::PANEL_NAME);
        }
    }

    public function testLoad()
    {
        $case = $this->caseItems();
        $items = $case->viewData('items');
        foreach ($items as $item) {
            $case->call('load', $item->id)
                 ->assertSet('action', ItemCrud::ACTION_UPDATE)
                 ->assertSet('modelId', $item->id)
                 ->assertSet('name', $item->name)
                 ->assertSet('createdAt', $item->created_at)
                 ->assertSet('updatedAt', $item->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', ItemCrud::PANEL_NAME);
        }
    }

    public function testSaveNew()
    {
        $newItem = fake()->text(50);
        $case = $this->validateCreateAction($this->caseItems());
        $case->emit('item.create', ['name' => $newItem])
            ->assertSet('action', ItemCrud::ACTION_UPDATE);
        $this->assertDatabaseHas('items', ['name' => $newItem]);
        $case->assertSet('modelId', Item::where('name', $newItem)->first()->id);
    }

    public function testUpdate()
    {
        $newItem = fake()->text(50);
        $case = $this->caseOpenToAction(ItemCrud::ACTION_UPDATE);
        $case->emit('item.update', ['name' => $newItem])
            ->assertSet('action', ItemCrud::ACTION_UPDATE);
        $this->assertDatabaseHas('items', ['name' => $newItem]);
    }

    /*
     * Multiple items.
     * */
    private function caseItems()
    {
        $itemWithoutBasket = Item::factory()->create();
        $itemWithBasket = BasketItem::factory()->create()->item;
        $case = Livewire::test(ItemCrud::class)
            ->assertSet('modelId', '')
            ->assertSet('action', '')
            ->assertSet('viewData', null)
            ->assertSet('name', '')
            // create button has to be visible
            ->assertSeeHtml('<button class="btn btn-primary mb-3" type="button" wire:click="setAction(\''.ItemCrud::ACTION_CREATE.'\')"><i class="bi bi-plus-circle me-3"></i>'.__('New Item').'</button>')
            // The delete button for the unused address has to be visible
            ->assertSeeHtml('<button class="btn btn-danger" type="button" wire:click="loadForDelete('.$itemWithoutBasket->id.')">')
            // The delete button for the address used by a shop has to be not visible in the dom
            ->assertDontSeeHtml('<button class="btn btn-danger" type="button" wire:click="loadForDelete('.$itemWithBasket->id.')">');
        $items = $case->viewData('items');
        $this->assertCount(2, $items);
        return $case;
    }

    private function validateCreateAction($case)
    {
        $case->call('setAction', ItemCrud::ACTION_CREATE)
             ->assertSet('action', ItemCrud::ACTION_CREATE)
             ->assertSet('modelId', '')
             ->assertSet('name', '')
             ->assertSet('createdAt', '')
             ->assertSet('updatedAt', '')
             ->assertSet('viewData', null);
        return $case;
    }

    // Create one address and open it for the given action.
    private function caseOpenToAction($action)
    {
        $unusedItem = Item::factory()->create();
        $case = Livewire::withQueryParams(['id' => $unusedItem->id, 'action' => $action])->test(ItemCrud::class)
            ->assertSet('modelId', $unusedItem->id)
            ->assertSet('action', $action)
            ->assertSet('viewData.id', $unusedItem->id)
            ->assertSet('name', $unusedItem->name)
            ->assertSet('createdAt', $unusedItem->created_at)
            ->assertSet('updatedAt', $unusedItem->updated_at);
        return $case;
    }

    private function validateDeletion($case, $item)
    {
        $case->emit('item.delete', $item->id)
             ->assertStatus(200);
        $deletedItem = Item::where('id', $item->id)->first();
        if ($item->basket_items_count == 0) {
            $this->assertNull($deletedItem);
            return $case;
        }
        $this->assertNotNull($deletedItem);
        return $case;
    }
}
