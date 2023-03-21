<?php

namespace Tests\Feature\Livewire\Crud;

use App\Http\Livewire\Crud\AddressCrud;
use App\Models\Address;
use App\Models\Shop;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AddressCrudTest extends TestCase
{
    use RefreshDatabase;

    public function testTheComponentCanRender()
    {
        Livewire::test(AddressCrud::class)->assertStatus(200);
    }

    public function testSetCreateAction()
    {
        $this->validateCreateAction($this->caseAddresses());
    }

    public function testLoadWithAction()
    {
        foreach (AddressCrud::ACTIONS as $action) {
            if ($action == AddressCrud::ACTION_CREATE) {
                continue;
            }
            $this->caseOpenToAction($action);
        }
    }

    public function testDelete()
    {

        $case = $this->caseAddresses();
        $addresses = $case->viewData('addresses');
        foreach ($addresses as $address) {
            $case = $this->validateDeletion($case, $address);
        }
    }

    public function testLoadForView()
    {
        $case = $this->caseAddresses();
        $addresses = $case->viewData('addresses');
        foreach ($addresses as $address) {
            $case->call('loadForView', $address->id)
                 ->assertSet('action', AddressCrud::ACTION_READ)
                 ->assertSet('modelId', $address->id)
                 ->assertSet('addressRaw', $address->raw)
                 ->assertSet('createdAt', $address->created_at)
                 ->assertSet('updatedAt', $address->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', AddressCrud::PANEL_NAME);
        }
    }

    public function testLoadForDelete()
    {
        $case = $this->caseAddresses();
        $addresses = $case->viewData('addresses');
        foreach ($addresses as $address) {
            $case->call('loadForDelete', $address->id)
                 ->assertSet('action', AddressCrud::ACTION_DELETE)
                 ->assertSet('modelId', $address->id)
                 ->assertSet('addressRaw', $address->raw)
                 ->assertSet('createdAt', $address->created_at)
                 ->assertSet('updatedAt', $address->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', AddressCrud::PANEL_NAME);
        }
    }

    public function testLoad()
    {
        $case = $this->caseAddresses();
        $addresses = $case->viewData('addresses');
        foreach ($addresses as $address) {
            $case->call('load', $address->id)
                 ->assertSet('action', AddressCrud::ACTION_UPDATE)
                 ->assertSet('modelId', $address->id)
                 ->assertSet('addressRaw', $address->raw)
                 ->assertSet('createdAt', $address->created_at)
                 ->assertSet('updatedAt', $address->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', AddressCrud::PANEL_NAME);
        }
    }

    public function testSaveNew()
    {
        $newAddress = fake()->address();
        $case = $this->validateCreateAction($this->caseAddresses());
        $case->emit('address.create', ['raw' => $newAddress])
            ->assertSet('action', AddressCrud::ACTION_UPDATE);
        $this->assertDatabaseHas('addresses', ['raw' => $newAddress]);
        $case->assertSet('modelId', Address::where('raw', $newAddress)->first()->id);
    }

    public function testUpdate()
    {
        $newAddress = fake()->address();
        $case = $this->caseOpenToAction(AddressCrud::ACTION_UPDATE);
        $case->emit('address.update', ['raw' => $newAddress])
            ->assertSet('action', AddressCrud::ACTION_UPDATE);
        $this->assertDatabaseHas('addresses', ['raw' => $newAddress]);
    }

    /*
     * Create 3 addresses, 1 address is used by a company, 1 address is used by a shop, 1 address is not used by any company or shop.
     * */
    private function caseAddresses()
    {
        $unusedAddress = Address::factory()->create();
        $shop = Shop::factory()->create();
        $case = Livewire::test(AddressCrud::class)
            ->assertSet('modelId', '')
            ->assertSet('action', '')
            ->assertSet('viewData', null)
            ->assertSet('addressRaw', '')
            // create button has to be visible
            ->assertSeeHtml('<button class="btn btn-primary mb-3" type="button" wire:click="setAction(\''.AddressCrud::ACTION_CREATE.'\')"><i class="bi bi-plus-circle me-3"></i>'.__('New Address').'</button>')
            // The delete button for the unused address has to be visible
            ->assertSeeHtml('<button class="btn btn-danger" type="button" wire:click="loadForDelete('.$unusedAddress->id.')">')
            // The delete button for the address used by a shop has to be not visible in the dom
            ->assertDontSeeHtml('<button class="btn btn-danger" type="button" wire:click="loadForDelete('.$shop->address_id.')">');
        $addresses = $case->viewData('addresses');
        $this->assertCount(3, $addresses);
        return $case;
    }

    // Create one address and open it for the given action.
    private function caseOpenToAction($action)
    {
        $unusedAddress = Address::factory()->create();
        $case = Livewire::withQueryParams(['id' => $unusedAddress->id, 'action' => $action])->test(AddressCrud::class)
            ->assertSet('modelId', $unusedAddress->id)
            ->assertSet('action', $action)
            ->assertSet('viewData.id', $unusedAddress->id)
            ->assertSet('addressRaw', $unusedAddress->raw)
            ->assertSet('createdAt', $unusedAddress->created_at)
            ->assertSet('updatedAt', $unusedAddress->updated_at);
        return $case;
    }

    private function validateCreateAction($case)
    {
        $case->call('setAction', AddressCrud::ACTION_CREATE)
             ->assertSet('action', AddressCrud::ACTION_CREATE)
             ->assertSet('modelId', '')
             ->assertSet('addressRaw', '')
             ->assertSet('createdAt', '')
             ->assertSet('updatedAt', '')
             ->assertSet('viewData', null);
        return $case;
    }

    private function validateDeletion($case, $address)
    {
        $case->emit('address.delete', $address->id)
             ->assertStatus(200);
        $deletedAddress = Address::where('id', $address->id)->first();
        if ($address->companies_count == 0 && $address->shops_count == 0) {
            $this->assertNull($deletedAddress);
            return $case;
        }
        $this->assertNotNull($deletedAddress);
        return $case;
    }
}
