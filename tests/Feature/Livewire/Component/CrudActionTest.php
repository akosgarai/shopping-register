<?php

namespace Tests\Feature\Livewire\Component;

use App\Http\Livewire\Component\CrudAction;
use App\Models\Item;
use App\Models\QuantityUnit;
use App\Models\Shop;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class CrudActionTest extends TestCase
{
    use RefreshDatabase;

    public function testTheComponentCanRender()
    {
        $modelNames = ['basket', 'shop', 'item', 'address', 'company'];
        foreach ($modelNames as $modelName) {
            Livewire::test(CrudAction::class, ['modelName' => $modelName])->assertStatus(200);
        }
    }

    public function testUpdateData()
    {
        // Update a basket data with create action.
        $modelName = 'basket';
        $eventData = [
            'action' => 'create',
            'basket' => [
                'shopId' => '',
            ],
            'formData' => [],
            'shops' =>  [],
            'items' => [],
            'viewData' => null,
        ];
        $case = Livewire::test(CrudAction::class, ['modelName' => $modelName])
            ->assertSet('modelName', $modelName)
            ->emit('crudaction.update', $eventData)
            ->assertSet('action', 'create')
            ->assertSet('formData', $eventData['formData'])
            ->assertSet('viewData', $eventData['viewData'])
            ->assertSet('modelData', $eventData['basket']);
        $eventDataNoModel = [
            'action' => 'create',
            'formData' => [],
            'viewData' => null,
        ];
        $case->emit('crudaction.update', $eventDataNoModel)
            ->assertSet('action', 'create')
            ->assertSet('formData', $eventData['formData'])
            ->assertSet('viewData', $eventData['viewData'])
            ->assertSet('modelData', $eventData['basket']);
    }

    public function testCreate()
    {
        // create a shop to be able to select it.
        $shop = Shop::factory()->create();
        // Update a basket data with create action.
        $modelName = 'basket';
        $eventData = [
            'action' => 'create',
            'basket' => [
                'shopId' => '',
            ],
            'formData' => [
                ['keyName' => 'shopId', 'type' => 'selectorshop', 'rules' => 'required|integer|exists:shops,id', 'readonly' => false, 'options' => Shop::with('address')->get()]
            ],
            'shops' =>  [],
            'items' => [],
            'viewData' => null,
        ];
        $case = Livewire::test(CrudAction::class, ['modelName' => $modelName])
            ->assertSet('modelName', $modelName)
            ->emit('crudaction.update', $eventData)
            ->assertSet('action', 'create')
            ->assertSet('formData', $eventData['formData'])
            ->assertSet('viewData', $eventData['viewData'])
            ->assertSet('modelData', $eventData['basket']);
        $case->call('create')
            ->assertHasErrors(['modelData.shopId' => 'required']);
        // set the shopId and try again
        $case->set('modelData.shopId', $shop->id)
            ->call('create')
            ->assertHasNoErrors()
            ->assertEmitted($modelName.'.create');
    }

    public function testUpdate()
    {
        $this->actingAs(User::factory()->create());
        // create a shop to be able to select it.
        $shop = Shop::factory()->create();
        // create an item to be able to select it.
        $item = Item::factory()->create();
        // Update a basket data with create action.
        $modelName = 'basket';
        $eventData = [
            'action' => 'create',
            'basket' => [
                'shopId' => '',
                'image' => null,
                'uploadedImage' => null,
            ],
            'formData' => [
                ['keyName' => 'shopId', 'type' => 'selectorshop', 'rules' => 'required|integer|exists:shops,id', 'readonly' => false, 'options' => Shop::with('address')->get()],
                [
                    'type' => 'basketitem',
                    'keyNameItem' => 'newItemId',
                    'options' => (new Item())->all(),
                    'rulesItem' => 'integer|exists:items,id',
                    'keyNamePrice' => 'newItemPrice',
                    'rulesPrice' => 'numeric',
                    'keyNameQuantityUnit' => 'newItemQuantityUnitId',
                    'quantityUnits' => (new QuantityUnit())->all(),
                    'rulesQuantityUnit' => 'integer|exists:quantity_units,id',
                    'keyNameQuantity' => 'newItemQuantity',
                    'rulesQuantity' => 'numeric',
                    'keyNameUnitPrice' => 'newItemUnitPrice',
                    'rulesQuantityUnitPrice' => 'numeric',
                ],
                ['keyName' => 'image', 'type' => 'imageinput', 'imageURL' => null, 'rules' => 'nullable|image', 'readonly' => false, 'label' => 'Image Label', 'target' => 'uploadedImage'],
            ],
            'shops' =>  [],
            'items' => [],
            'viewData' => null,
        ];
        $case = Livewire::test(CrudAction::class, ['modelName' => $modelName])
            ->assertSet('modelName', $modelName)
            ->emit('crudaction.update', $eventData)
            ->assertSet('action', 'create')
            ->assertSet('formData', $eventData['formData'])
            ->assertSet('viewData', $eventData['viewData'])
            ->assertSet('modelData', $eventData['basket']);
        $case->call('update')
            ->assertHasErrors(['modelData.shopId' => 'required']);
        // set the shopId and try again
        $case->set('modelData.shopId', $shop->id)
            ->set('modelData.newItemId', $item->id)
            ->set('modelData.newItemPrice', 1)
            ->set('modelData.newItemQuantityUnitId', 1)
            ->set('modelData.image', UploadedFile::fake()->image('sample.png', 640, 480))
            ->call('update')
            ->assertHasNoErrors()
            ->assertEmitted($modelName.'.update');
    }

    public function testDelete()
    {
        // create a shop
        $shop = Shop::factory()->create();
        $modelName = 'shop';
        $eventData = [
            'action' => 'delete',
            'shop' => [
                'id' => $shop->id,
            ],
            'formData' => [
                ['keyName' => 'shopId', 'type' => 'selectorshop', 'rules' => 'required|integer|exists:shops,id', 'readonly' => false, 'options' => Shop::with('address')->get()]
            ],
            'viewData' => null,
        ];
        Livewire::test(CrudAction::class, ['modelName' => $modelName])
            ->assertSet('modelName', $modelName)
            ->emit('crudaction.update', $eventData)
            ->call('delete')
            ->assertEmitted($modelName.'.delete', $shop->id)
        ;
    }
}
