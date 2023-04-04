<?php

namespace Tests\Feature\Livewire\Crud;

use App\Http\Livewire\Crud\CompanyCrud;
use App\Models\Address;
use App\Models\Company;
use App\Models\Shop;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CompanyCrudTest extends TestCase
{
    use RefreshDatabase;

    public function testTheComponentCanRender()
    {
        Livewire::test(CompanyCrud::class)->assertStatus(200);
    }

    public function testSetCreateAction()
    {
        $this->validateCreateAction($this->caseCompanies());
    }

    public function testLoadWithAction()
    {
        foreach (CompanyCrud::ACTIONS as $action) {
            if ($action == CompanyCrud::ACTION_CREATE) {
                continue;
            }
            $this->caseOpenToAction($action);
        }
    }

    public function testLoadNotExistingAction()
    {
        // init the action to list and then try to load with a not existing action
        // the action should not be changed
        $case = $this->caseCompanies();
        $case->call('setAction', 'not-existing-action')
             ->assertSet('action', '');
    }

    public function testDelete()
    {
        $case = $this->caseCompanies();
        $companies = $case->viewData('companies');
        foreach ($companies as $company) {
            $case = $this->validateDeletion($case, $company);
        }
    }

    public function testLoadForView()
    {
        $case = $this->caseCompanies();
        $companies = $case->viewData('companies');
        foreach ($companies as $company) {
            $case->call('loadForView', $company->id)
                 ->assertSet('action', CompanyCrud::ACTION_READ)
                 ->assertSet('modelId', $company->id)
                 ->assertSet('name', $company->name)
                 ->assertSet('taxNumber', $company->tax_number)
                 ->assertSet('address', $company->address->id)
                 ->assertSet('createdAt', $company->created_at)
                 ->assertSet('updatedAt', $company->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', CompanyCrud::PANEL_NAME);
        }
    }

    public function testLoadForDelete()
    {
        $case = $this->caseCompanies();
        $companies = $case->viewData('companies');
        foreach ($companies as $company) {
            $case->call('loadForDelete', $company->id)
                 ->assertSet('action', CompanyCrud::ACTION_DELETE)
                 ->assertSet('modelId', $company->id)
                 ->assertSet('name', $company->name)
                 ->assertSet('taxNumber', $company->tax_number)
                 ->assertSet('address', $company->address->id)
                 ->assertSet('createdAt', $company->created_at)
                 ->assertSet('updatedAt', $company->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', CompanyCrud::PANEL_NAME);
        }
    }

    public function testLoad()
    {
        $case = $this->caseCompanies();
        $companies = $case->viewData('companies');
        foreach ($companies as $company) {
            $case->call('load', $company->id)
                 ->assertSet('action', CompanyCrud::ACTION_UPDATE)
                 ->assertSet('modelId', $company->id)
                 ->assertSet('name', $company->name)
                 ->assertSet('taxNumber', $company->tax_number)
                 ->assertSet('address', $company->address->id)
                 ->assertSet('createdAt', $company->created_at)
                 ->assertSet('updatedAt', $company->updated_at)
                 ->assertEmitted('crudaction.update')
                 ->assertEmitted('panel.open', CompanyCrud::PANEL_NAME);
        }
    }

    public function testSaveNew()
    {
        $newName = fake()->text(50);
        $newTaxNumber = fake()->randomNumber(9, true) . fake()->randomNumber(2, true);
        $newAddress = Address::factory()->create()->id;
        $case = $this->validateCreateAction($this->caseCompanies());
        $case->emit('company.create', ['name' => $newName, 'taxNumber' => $newTaxNumber, 'address' => $newAddress])
            ->assertHasNoErrors(['name', 'taxNumber', 'address'])
            ->assertSet('action', CompanyCrud::ACTION_UPDATE);
        $this->assertDatabaseHas('companies', ['name' => $newName, 'tax_number' => $newTaxNumber, 'address_id' => $newAddress]);
        $case->assertSet('modelId', Company::where('tax_number', $newTaxNumber)->first()->id);
    }

    public function testUpdate()
    {
        $newName = fake()->text(50);
        $newTaxNumber = fake()->randomNumber(9, true) . fake()->randomNumber(2, true);
        $newAddress = Address::factory()->create()->id;
        $case = $this->caseOpenToAction(CompanyCrud::ACTION_UPDATE);
        $case->emit('company.update', ['name' => $newName, 'taxNumber' => $newTaxNumber, 'address' => $newAddress])
            ->assertSet('action', CompanyCrud::ACTION_UPDATE);
        $this->assertDatabaseHas('companies', ['name' => $newName, 'tax_number' => $newTaxNumber, 'address_id' => $newAddress]);
    }

    /*
     * Multiple companies.
     * */
    private function caseCompanies()
    {
        $company = Company::factory()->create();
        $companyWithShop = Shop::factory()->create()->company;
        $case = Livewire::test(CompanyCrud::class)
            ->assertSet('modelId', '')
            ->assertSet('action', '')
            ->assertSet('viewData', null)
            ->assertSet('name', '')
            ->assertSet('taxNumber', '')
            ->assertSet('address', '')
            // create button has to be visible
            ->assertSeeHtml('<button class="btn btn-primary mb-3" type="button" wire:click="setAction(\''.CompanyCrud::ACTION_CREATE.'\')"><i class="bi bi-plus-circle me-3"></i>'.__('New Company').'</button>')
            // The delete button for the unused address has to be visible
            ->assertSeeHtml('<button class="btn btn-danger" type="button" wire:click="loadForDelete('.$company->id.')">')
            // The delete button for the address used by a shop has to be not visible in the dom
            ->assertDontSeeHtml('<button class="btn btn-danger" type="button" wire:click="loadForDelete('.$companyWithShop->id.')">');
        $companies = $case->viewData('companies');
        $this->assertCount(2, $companies);
        // check the search also
        // search for the company name without shop
        $case->set('search', $company->name)
            ->assertSet('search', $company->name)
            ->assertSeeHtml('<td>'.$company->tax_number.'</td>')
            ->assertDontSeeHtml('<td>'.$companyWithShop->tax_number.'</td>');
        // search for the company tax_number with shop
        $case->set('search', $companyWithShop->tax_number)
            ->assertSet('search', $companyWithShop->tax_number)
            ->assertDontSeeHtml('<td>'.$company->tax_number.'</td>')
            ->assertSeeHtml('<td>'.$companyWithShop->tax_number.'</td>');
        // reset the search
        $case->set('search', '')
            ->assertSet('search', '')
            ->assertSeeHtml('<td>'.$company->tax_number.'</td>')
            ->assertSeeHtml('<td>'.$companyWithShop->tax_number.'</td>');
        return $case;
    }

    private function validateCreateAction($case)
    {
        $case->call('setAction', CompanyCrud::ACTION_CREATE)
             ->assertSet('action', CompanyCrud::ACTION_CREATE)
             ->assertSet('modelId', '')
             ->assertSet('name', '')
             ->assertSet('taxNumber', '')
             ->assertSet('address', '')
             ->assertSet('createdAt', '')
             ->assertSet('updatedAt', '')
             ->assertSet('viewData', null);
        return $case;
    }

    // Create one company and open it for the given action.
    private function caseOpenToAction($action)
    {
        $company = Company::factory()->create();
        $case = Livewire::withQueryParams(['id' => $company->id, 'action' => $action])->test(CompanyCrud::class)
            ->assertSet('modelId', $company->id)
            ->assertSet('action', $action)
            ->assertSet('viewData.id', $company->id)
            ->assertSet('name', $company->name)
            ->assertSet('taxNumber', $company->tax_number)
            ->assertSet('address', $company->address->id)
            ->assertSet('createdAt', $company->created_at)
            ->assertSet('updatedAt', $company->updated_at);
        return $case;
    }

    private function validateDeletion($case, $company)
    {
        $case->emit('company.delete', $company->id)
             ->assertStatus(200);
        $deletedCompany = Company::where('id', $company->id)->first();
        if ($company->shops_count == 0) {
            $this->assertNull($deletedCompany);
            return $case;
        }
        $this->assertNotNull($deletedCompany);
        return $case;
    }
}
