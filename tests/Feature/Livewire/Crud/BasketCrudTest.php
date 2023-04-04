<?php

namespace Tests\Feature\Livewire\Crud;

use App\Http\Livewire\Crud\BasketCrud;
use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Item;
use App\Models\Shop;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BasketCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function testTheComponentCanRender()
    {
        $this->actAsUser();
        Livewire::test(BasketCrud::class)->assertStatus(200);
    }

    public function testSetCreateAction()
    {
        $this->actAsUser();
        $this->validateCreateAction($this->caseBaskets());
    }

    public function testLoadWithAction()
    {
        $this->actAsUser();
        foreach (BasketCrud::ACTIONS as $action) {
            if ($action == BasketCrud::ACTION_CREATE) {
                continue;
            }
            $this->caseOpenToAction($action);
        }
    }

    public function testLoadNotExistingAction()
    {
        $this->actAsUser();
        // init the action to list and then try to load with a not existing action
        // the action should not be changed
        $case = $this->caseBaskets();
        $case->call('setAction', 'not-existing-action')
             ->assertSet('action', '');
    }

    public function testDelete()
    {
        $this->actAsUser();
        $case = $this->caseBaskets();
        $baskets = $case->viewData('baskets');
        foreach ($baskets as $basket) {
            $case = $this->validateDeletion($case, $basket);
        }
    }

    public function testLoadForView()
    {
        $this->actAsUser();
        $case = $this->caseBaskets();
        $baskets = $case->viewData('baskets');
        foreach ($baskets as $basket) {
            // Extend the basket with the items
            $basketItems = $basket->basketItems->toArray();
            foreach ($basketItems as $key => $basketItem) {
                $basketItems[$key]['item'] = Item::find($basketItem['item_id'])->toArray();
            }
            $case->call('loadForView', $basket->id)
                 ->assertSet('modelId', $basket->id)
                 ->assertSet('action', BasketCrud::ACTION_READ)
                 ->assertSet('viewData.id', $basket->id)
                 ->assertSet('shopId', $basket->shop->id)
                 ->assertSet('date', $basket->date)
                 ->assertSet('name', $basket->name)
                 ->assertSet('total', $basket->total)
                 ->assertSet('receiptId', $basket->receipt_id)
                 ->assertSet('imageURL', $basket->image_url)
                 ->assertSet('image', null)
                 ->assertSet('originalImage', null)
                 ->assertSet('items', $basketItems)
                 ->assertSet('createdAt', $basket->created_at)
                 ->assertSet('updatedAt', $basket->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', BasketCrud::PANEL_NAME);
        }
    }

    public function testLoadForDelete()
    {
        $this->actAsUser();
        $case = $this->caseBaskets();
        $baskets = $case->viewData('baskets');
        foreach ($baskets as $basket) {
            // Extend the basket with the items
            $basketItems = $basket->basketItems->toArray();
            foreach ($basketItems as $key => $basketItem) {
                $basketItems[$key]['item'] = Item::find($basketItem['item_id'])->toArray();
            }
            $case->call('loadForDelete', $basket->id)
                 ->assertSet('modelId', $basket->id)
                 ->assertSet('action', BasketCrud::ACTION_DELETE)
                 ->assertSet('viewData.id', $basket->id)
                 ->assertSet('shopId', $basket->shop->id)
                 ->assertSet('date', $basket->date)
                 ->assertSet('name', $basket->name)
                 ->assertSet('total', $basket->total)
                 ->assertSet('receiptId', $basket->receipt_id)
                 ->assertSet('imageURL', $basket->image_url)
                 ->assertSet('image', null)
                 ->assertSet('originalImage', null)
                 ->assertSet('items', $basketItems)
                 ->assertSet('createdAt', $basket->created_at)
                 ->assertSet('updatedAt', $basket->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', BasketCrud::PANEL_NAME);
        }
    }

    public function testLoad()
    {
        $this->actAsUser();
        $case = $this->caseBaskets();
        $baskets = $case->viewData('baskets');
        foreach ($baskets as $basket) {
            // Extend the basket with the items
            $basketItems = $basket->basketItems->toArray();
            foreach ($basketItems as $key => $basketItem) {
                $basketItems[$key]['item'] = Item::find($basketItem['item_id'])->toArray();
            }
            $case->call('load', $basket->id)
                 ->assertSet('modelId', $basket->id)
                 ->assertSet('action', BasketCrud::ACTION_UPDATE)
                 ->assertSet('viewData.id', $basket->id)
                 ->assertSet('shopId', $basket->shop->id)
                 ->assertSet('date', $basket->date)
                 ->assertSet('name', $basket->name)
                 ->assertSet('total', $basket->total)
                 ->assertSet('receiptId', $basket->receipt_id)
                 ->assertSet('imageURL', $basket->image_url)
                 ->assertSet('image', null)
                 ->assertSet('originalImage', null)
                 ->assertSet('items', $basketItems)
                 ->assertSet('createdAt', $basket->created_at)
                 ->assertSet('updatedAt', $basket->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', BasketCrud::PANEL_NAME);
        }
    }

    public function testSaveNewWithItems()
    {
        $this->actAsUser();
        $shop = Shop::factory()->create();
        $date = fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s');
        $total = 100.00;
        $receiptId = fake()->randomLetter() . fake()->randomNumber(9, true);
        $item = Item::factory()->create();
        $items = [
            ['item_id' => $item->id, 'quantity' => 1, 'price' => 50.00, 'unit_price' => 50.00, 'quantity_unit_id' => 1],
            ['item_id' => $item->id, 'quantity' => 1, 'price' => 50.00, 'unit_price' => 50.00, 'quantity_unit_id' => 1],
        ];
        $case = $this->validateCreateAction($this->caseBaskets());
        $case->emit('basket.create', [
            'shopId' => $shop->id,
            'date' => $date,
            'total' => $total,
            'receiptId' => $receiptId,
            'items' => $items,
        ])
            ->assertHasNoErrors(['shopId', 'date', 'total', 'receiptId', 'items', 'image'])
            ->assertSet('action', BasketCrud::ACTION_UPDATE);
        $this->assertDatabaseHas('baskets', ['shop_id' => $shop->id, 'receipt_id' => $receiptId, 'date' => $date]);
        $case->assertSet('modelId', Basket::where('shop_id', $shop->id)->where('receipt_id', $receiptId)->where('date', $date)->first()->id);
    }

    public function testSaveNewWithoutItems()
    {
        $this->actAsUser();
        $shop = Shop::factory()->create();
        $date = fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s');
        $total = 100.00;
        $receiptId = fake()->randomLetter() . fake()->randomNumber(9, true);
        $items = [];
        $case = $this->validateCreateAction($this->caseBaskets());
        $case->emit('basket.create', [
            'shopId' => $shop->id,
            'date' => $date,
            'total' => $total,
            'receiptId' => $receiptId,
            'items' => $items,
            'image' => null,
        ])
            ->assertHasNoErrors(['shopId', 'date', 'total', 'receiptId', 'items', 'image'])
            ->assertSet('action', BasketCrud::ACTION_UPDATE);
        $this->assertDatabaseHas('baskets', ['shop_id' => $shop->id, 'receipt_id' => $receiptId, 'date' => $date]);
        $case->assertSet('modelId', Basket::where('shop_id', $shop->id)->where('receipt_id', $receiptId)->where('date', $date)->first()->id);
    }

    public function testUpdate()
    {
        $this->actAsUser();
        $shop = Shop::factory()->create();
        $date = fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s');
        $total = 100.00;
        $receiptId = fake()->randomLetter() . fake()->randomNumber(9, true);
        $item = Item::factory()->create();
        $items = [
            ['item_id' => $item->id, 'quantity' => 1, 'price' => 50.00, 'unit_price' => 50.00, 'quantity_unit_id' => 1],
            ['item_id' => $item->id, 'quantity' => 1, 'price' => 50.00, 'unit_price' => 50.00, 'quantity_unit_id' => 1],
        ];
        $case = $this->caseOpenToAction(BasketCrud::ACTION_UPDATE);
        $case->emit('basket.update', [
            'shopId' => $shop->id,
            'date' => $date,
            'total' => $total,
            'receiptId' => $receiptId,
            'items' => $items,
            'image' => null,
            'uploadedImage' => '',
        ])
            ->assertHasNoErrors(['shopId', 'date', 'total', 'receiptId', 'items', 'image'])
            ->assertSet('action', BasketCrud::ACTION_UPDATE);
        $this->assertDatabaseHas('baskets', ['shop_id' => $shop->id, 'receipt_id' => $receiptId, 'date' => $date]);
    }

    public function testAddBasketItem()
    {
        $this->actAsUser();
        $item = Item::factory()->create();
        $price = 50.00;
        $case = $this->caseOpenToAction(BasketCrud::ACTION_UPDATE);
        $originalPrice = $case->viewData('total');
        $case->set('newItemQuantityUnitId', 1)
            ->emit('basket.addBasketItem', $item->id, $price)
            ->assertHasNoErrors(['newItemId', 'newItemPrice', 'newItemQuantity', 'newItemQuantityUnitId'])
            ->assertSet('action', BasketCrud::ACTION_UPDATE)
            // The total has to be changed to the value of the new item, as it is the first item
            ->assertNotSet('total', $originalPrice)
            ->assertSet('total', $price)
            ->assertSet('newItemId', '')
            ->assertSet('newItemPrice', '')
            ->assertCount('items', 1)
            ->assertEmitted('crudaction.update');
            // Add another item and check the total price.
        $case->set('newItemQuantityUnitId', 1)
            ->emit('basket.addBasketItem', $item->id, $price)
            ->assertHasNoErrors(['newItemId', 'newItemPrice', 'newItemQuantity', 'newItemQuantityUnitId'])
            ->assertSet('action', BasketCrud::ACTION_UPDATE)
            ->assertSet('total', $price * 2)
            ->assertSet('newItemId', '')
            ->assertSet('newItemPrice', '')
            ->assertCount('items', 2)
            ->assertEmitted('crudaction.update');
    }

    public function testDeleteBasketItem()
    {
        $this->actAsUser();
        $case = $this->caseOpenToAction(BasketCrud::ACTION_UPDATE)->set('newItemQuantityUnitId', 1);
        $item = Item::factory()->create();
        $price = 50.00;
        $numItems = 3;
        for ($i = 0; $i < $numItems; $i++) {
            $case->emit('basket.addBasketItem', $item->id, $price)
                ->assertHasNoErrors(['newItemId', 'newItemPrice', 'newItemQuantity', 'newItemQuantityUnitId'])
                ->assertSet('action', BasketCrud::ACTION_UPDATE)
                // The total has to be changed to the value of the new item, as it is the first item
                ->assertSet('total', $price * ($i + 1))
                ->assertSet('newItemId', '')
                ->assertSet('newItemPrice', '')
                ->assertCount('items', $i + 1)
                ->assertEmitted('crudaction.update');
        }
        for ($i = 0; $i < $numItems; $i++) {
            $case->emit('basket.deleteBasketItem', $i)
                ->assertSet('action', BasketCrud::ACTION_UPDATE)
                ->assertSet('total', $price * ($numItems - $i - 1))
                ->assertCount('items', $numItems - $i - 1)
                ->assertEmitted('crudaction.update');
        }
    }

    public function testDeleteBasketImage()
    {
        $this->actAsUser();
        $this->caseOpenToAction(BasketCrud::ACTION_UPDATE)
            ->call('deleteBasketImage')
            ->assertSet('action', BasketCrud::ACTION_UPDATE)
            ->assertSet('basketImageURL', '')
            ->assertSet('basketImage', null);
    }

    private function actAsUser()
    {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /*
     * Setup multiple baskets.
     * */
    private function caseBaskets()
    {
        $basket = Basket::factory()->make(['user_id' => $this->user->id]);
        $basket->save();
        $basketWithItems = Basket::factory()->make(['user_id' => $this->user->id]);
        $basketWithItems->save();
        BasketItem::factory()->make(['basket_id' => $basketWithItems->id])->save();
        $case = Livewire::test(BasketCrud::class)
            ->assertSet('modelId', '')
            ->assertSet('action', '')
            ->assertSet('viewData', null)
            ->assertSet('shopId', '')
            ->assertSet('date', '')
            ->assertSet('total', 0.0)
            ->assertSet('receiptId', '')
            ->assertSet('imageURL', '')
            ->assertSet('image', null)
            ->assertSet('originalImage', '')
            ->assertSet('items', [])
            ->assertSet('newItemId', '')
            ->assertSet('newItemPrice', '')
            ->assertSet('newItemQuantity', 1)
            ->assertSet('newItemQuantityUnitId', '')
            ->assertSet('newItemUnitPrice', '')
            // create button has to be visible
            ->assertSeeHtml('<button class="btn btn-primary mb-3" type="button" wire:click="setAction(\''.BasketCrud::ACTION_CREATE.'\')"><i class="bi bi-plus-circle me-3"></i>'.__('New Basket').'</button>')
            // The delete button for the unused address has to be visible
            ->assertSeeHtml('<button class="btn btn-danger" type="button" wire:click="loadForDelete('.$basket->id.')">')
            // The delete button for the address used by a shop has to be not visible in the dom
            ->assertDontSeeHtml('<button class="btn btn-danger" type="button" wire:click="loadForDelete('.$basketWithItems->id.')">');
        ;
        $baskets = $case->viewData('baskets');
        $this->assertCount(2, $baskets);
        // check the search also
        // search for the first basket
        $case->set('search', $basket->receipt_id)
            ->assertSet('search', $basket->receipt_id)
            ->assertSeeHtml('<th scope="row">'.$basket->id.'</th>')
            ->assertDontSeeHtml('<th scope="row">'.$basketWithItems->id.'</th>');
        // reset the search
        $case->set('search', '')
            ->assertSet('search', '')
            ->assertSeeHtml('<th scope="row">'.$basket->id.'</th>')
            ->assertSeeHtml('<th scope="row">'.$basketWithItems->id.'</th>');
        return $case;
    }

    private function validateCreateAction($case)
    {
        $case->call('setAction', BasketCrud::ACTION_CREATE)
            ->assertSet('action', BasketCrud::ACTION_CREATE)
            ->assertSet('modelId', '')
            ->assertSet('viewData', null)
            ->assertSet('shopId', '')
            ->assertSet('date', '')
            ->assertSet('total', 0.0)
            ->assertSet('receiptId', '')
            ->assertSet('imageURL', '')
            ->assertSet('image', null)
            ->assertSet('originalImage', '')
            ->assertSet('items', [])
            ->assertSet('newItemId', '')
            ->assertSet('newItemPrice', '')
            ->assertSet('newItemQuantity', 1)
            ->assertSet('newItemQuantityUnitId', '')
            ->assertSet('newItemUnitPrice', '');
        return $case;
    }

    // Create one shop and open it for the given action.
    private function caseOpenToAction($action)
    {
        $basket = Basket::factory()->make(['user_id' => $this->user->id]);
        $basket->save();
        $case = Livewire::withQueryParams(['id' => $basket->id, 'action' => $action])->test(BasketCrud::class)
            ->assertSet('modelId', $basket->id)
            ->assertSet('action', $action)
            ->assertSet('viewData.id', $basket->id)
            ->assertSet('shopId', $basket->shop->id)
            // format the date to the format of the database
            ->assertSet('date', $basket->date->format('Y-m-d H:i:s'))
            ->assertSet('name', $basket->name)
            ->assertSet('total', $basket->total)
            ->assertSet('receiptId', $basket->receipt_id)
            ->assertSet('imageURL', '')
            ->assertSet('image', null)
            ->assertSet('originalImage', $basket->receipt_url)
            ->assertSet('items', [])
            ->assertSet('createdAt', $basket->created_at)
            ->assertSet('updatedAt', $basket->updated_at)

            ->assertSet('newItemId', '')
            ->assertSet('newItemPrice', '')
            ->assertSet('newItemQuantity', 1)
            ->assertSet('newItemQuantityUnitId', '')
            ->assertSet('newItemUnitPrice', '');
        return $case;
    }

    private function validateDeletion($case, $basket)
    {
        $case->emit('basket.delete', $basket->id)
             ->assertStatus(200);
        $deletedBasket = Basket::where('id', $basket->id)->first();
        if ($basket->basket_items_count == 0) {
            $this->assertNull($deletedBasket);
            return $case;
        }
        $this->assertNotNull($deletedBasket);
        return $case;
    }
}
