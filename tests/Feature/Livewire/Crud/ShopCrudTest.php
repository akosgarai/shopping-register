<?php

namespace Tests\Feature\Livewire\Crud;

use App\Http\Livewire\Crud\ShopCrud;
use App\Models\Address;
use App\Models\Basket;
use App\Models\Company;
use App\Models\Shop;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShopCrudTest extends TestCase
{
    use RefreshDatabase;

    public function testTheComponentCanRender()
    {
        Livewire::test(ShopCrud::class)->assertStatus(200);
    }

    public function testSetCreateAction()
    {
        $this->validateCreateAction($this->caseShops());
    }

    public function testLoadWithAction()
    {
        foreach (ShopCrud::ACTIONS as $action) {
            if ($action == ShopCrud::ACTION_CREATE) {
                continue;
            }
            $this->caseOpenToAction($action);
        }
    }

    public function testLoadNotExistingAction()
    {
        // init the action to list and then try to load with a not existing action
        // the action should not be changed
        $case = $this->caseShops();
        $case->call('setAction', 'not-existing-action')
             ->assertSet('action', '');
    }

    public function testDelete()
    {
        $case = $this->caseShops();
        $shops = $case->viewData('shops');
        foreach ($shops as $shop) {
            $case = $this->validateDeletion($case, $shop);
        }
    }

    public function testLoadForView()
    {
        $case = $this->caseShops();
        $shops = $case->viewData('shops');
        foreach ($shops as $shop) {
            $case->call('loadForView', $shop->id)
                 ->assertSet('action', ShopCrud::ACTION_READ)
                 ->assertSet('modelId', $shop->id)
                 ->assertSet('name', $shop->name)
                 ->assertSet('company', $shop->company->id)
                 ->assertSet('address', $shop->address->id)
                 ->assertSet('createdAt', $shop->created_at)
                 ->assertSet('updatedAt', $shop->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', ShopCrud::PANEL_NAME);
        }
    }

    public function testLoadForDelete()
    {
        $case = $this->caseShops();
        $shops = $case->viewData('shops');
        foreach ($shops as $shop) {
            $case->call('loadForDelete', $shop->id)
                 ->assertSet('action', ShopCrud::ACTION_DELETE)
                 ->assertSet('modelId', $shop->id)
                 ->assertSet('name', $shop->name)
                 ->assertSet('company', $shop->company->id)
                 ->assertSet('address', $shop->address->id)
                 ->assertSet('createdAt', $shop->created_at)
                 ->assertSet('updatedAt', $shop->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', ShopCrud::PANEL_NAME);
        }
    }

    public function testLoad()
    {
        $case = $this->caseShops();
        $shops = $case->viewData('shops');
        foreach ($shops as $shop) {
            $case->call('load', $shop->id)
                 ->assertSet('action', ShopCrud::ACTION_UPDATE)
                 ->assertSet('modelId', $shop->id)
                 ->assertSet('name', $shop->name)
                 ->assertSet('company', $shop->company->id)
                 ->assertSet('address', $shop->address->id)
                 ->assertSet('createdAt', $shop->created_at)
                 ->assertSet('updatedAt', $shop->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', ShopCrud::PANEL_NAME);
        }
    }

    public function testSaveNew()
    {
        $newName = fake()->text(50);
        $newCompany = Company::factory()->create()->id;
        $newAddress = Address::factory()->create()->id;
        $case = $this->validateCreateAction($this->caseShops());
        $case->emit('shop.create', ['name' => $newName, 'company' => $newCompany, 'address' => $newAddress])
            ->assertHasNoErrors(['name', 'company', 'address'])
            ->assertSet('action', ShopCrud::ACTION_UPDATE);
        $this->assertDatabaseHas('shops', ['name' => $newName, 'company_id' => $newCompany, 'address_id' => $newAddress]);
        $case->assertSet('modelId', Shop::where('name', $newName)->where('company_id', $newCompany)->where('address_id', $newAddress)->first()->id);
    }

    public function testUpdate()
    {
        $newName = fake()->text(50);
        $newCompany = Company::factory()->create()->id;
        $newAddress = Address::factory()->create()->id;
        $case = $this->caseOpenToAction(ShopCrud::ACTION_UPDATE);
        $case->emit('shop.update', ['name' => $newName, 'company' => $newCompany, 'address' => $newAddress])
            ->assertSet('action', ShopCrud::ACTION_UPDATE);
        $this->assertDatabaseHas('shops', ['name' => $newName, 'company_id' => $newCompany, 'address_id' => $newAddress]);
    }

    /*
     * Setup multiple shop.
     * */
    private function caseShops()
    {
        $shop = Shop::factory()->create();
        $shopWithBasket = Basket::factory()->create()->shop;
        $case = Livewire::test(ShopCrud::class)
            ->assertSet('modelId', '')
            ->assertSet('action', '')
            ->assertSet('viewData', null)
            ->assertSet('name', '')
            ->assertSet('taxNumber', '')
            ->assertSet('address', '')
            // create button has to be visible
            ->assertSeeHtml('<button class="btn btn-primary mb-3" type="button" wire:click="setAction(\''.ShopCrud::ACTION_CREATE.'\')"><i class="bi bi-plus-circle me-3"></i>'.__('New Shop').'</button>')
            // The delete button for the unused address has to be visible
            ->assertSeeHtml('<button class="btn btn-danger" type="button" wire:click="loadForDelete('.$shop->id.')">')
            // The delete button for the address used by a shop has to be not visible in the dom
            ->assertDontSeeHtml('<button class="btn btn-danger" type="button" wire:click="loadForDelete('.$shopWithBasket->id.')">');
        $shops = $case->viewData('shops');
        $this->assertCount(2, $shops);
        return $case;
    }

    private function validateCreateAction($case)
    {
        $case->call('setAction', ShopCrud::ACTION_CREATE)
             ->assertSet('action', ShopCrud::ACTION_CREATE)
             ->assertSet('modelId', '')
             ->assertSet('name', '')
             ->assertSet('company', '')
             ->assertSet('address', '')
             ->assertSet('createdAt', '')
             ->assertSet('updatedAt', '')
             ->assertSet('viewData', null);
        return $case;
    }

    // Create one shop and open it for the given action.
    private function caseOpenToAction($action)
    {
        $shop = Shop::factory()->create();
        $case = Livewire::withQueryParams(['id' => $shop->id, 'action' => $action])->test(ShopCrud::class)
            ->assertSet('modelId', $shop->id)
            ->assertSet('action', $action)
            ->assertSet('viewData.id', $shop->id)
            ->assertSet('name', $shop->name)
            ->assertSet('company', $shop->company->id)
            ->assertSet('address', $shop->address->id)
            ->assertSet('createdAt', $shop->created_at)
            ->assertSet('updatedAt', $shop->updated_at);
        return $case;
    }

    private function validateDeletion($case, $shop)
    {
        $case->emit('shop.delete', $shop->id)
             ->assertStatus(200);
        $deletedShop = Shop::where('id', $shop->id)->first();
        if ($shop->baskets_count == 0) {
            $this->assertNull($deletedShop);
            return $case;
        }
        $this->assertNotNull($deletedShop);
        return $case;
    }
}
