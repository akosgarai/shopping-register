<?php

namespace Tests\Feature\Livewire\Component\Scan;

use App\Http\Livewire\Component\Scan\Shop;
use App\Models\Address;
use App\Models\Company;
use App\Services\DataPredictionService;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    const TEST_SCANNED_NAME = 'TestName';
    const TEST_SCANNED_ADDRESS = 'TestAddress';

    /** @test */
    public function testTheComponentCanRender()
    {
        Livewire::test(Shop::class)->assertStatus(200);
    }

    public function testTheInitialParametersWithoutCompany()
    {
        Livewire::test(Shop::class, ['scannedName' => self::TEST_SCANNED_NAME, 'scannedAddress' => self::TEST_SCANNED_ADDRESS])
            ->assertSet('name', self::TEST_SCANNED_NAME)
            ->assertSet('address', self::TEST_SCANNED_ADDRESS)
            ->assertSet('shopCompany', '')
            ->assertSee(__('You have to setup the company to be able to set the shop.'));
    }

    public function testTheInitialParametersWithCompany()
    {
        $company = $this->createCompany();
        Livewire::test(Shop::class, ['scannedName' => self::TEST_SCANNED_NAME, 'scannedAddress' => self::TEST_SCANNED_ADDRESS, 'shopCompany' => $company->id])
            ->assertSet('name', self::TEST_SCANNED_NAME)
            ->assertSet('address', self::TEST_SCANNED_ADDRESS)
            ->assertSet('shopCompany', $company->id)
            ->assertDontSee(__('You have to setup the company to be able to set the shop.'));
    }

    public function testUpdateBasketEvent()
    {
        $emptyValue = '';
        $company = $this->createCompany();
        Livewire::test(Shop::class, ['scannedName' => $emptyValue, 'scannedAddress' => $emptyValue])
            ->assertSet('name', $emptyValue)
            ->assertSet('address', $emptyValue)
            ->assertSet('shopCompany', $emptyValue)
            ->assertSee(__('You have to setup the company to be able to set the shop.'))
            ->emit('basket.data.extracted', [
                'marketName' => self::TEST_SCANNED_NAME,
                'marketAddress' => self::TEST_SCANNED_ADDRESS,
                'companyId' => $company->id,
            ])
            ->assertSet('name', self::TEST_SCANNED_NAME)
            ->assertSet('address', self::TEST_SCANNED_ADDRESS)
            ->assertSet('shopCompany', $company->id);
    }

    public function testAddressSelection()
    {
        $company = $this->createCompany();
        $emptyAddress = '';
        Livewire::test(Shop::class, ['scannedName' => self::TEST_SCANNED_NAME, 'scannedAddress' => $emptyAddress, 'shopCompany' => $company->id])
            ->assertSet('name', self::TEST_SCANNED_NAME)
            ->assertSet('address', $emptyAddress)
            ->assertSet('shopCompany', $company->id)
            ->assertDontSee(__('You have to setup the company to be able to set the shop.'))
            // one suggestion due to the company.
            ->assertCount('addressSuggestions', 1)
            // Should be able to save the address even with empty address.
            ->assertSet('allowSaveAddress', true)
            ->assertSee(__('New Address'))
            ->assertSee(__('Back to Company'))
            // Save Shop shouldn't be visible
            ->assertSet('allowSaveShop', false)
            ->assertDontSee(__('New Shop'))
            // invalid address (required) -> should not be inserted, should have error
            ->call('insertNew', Shop::DATA_TYPE_ADDRESS)
            ->assertHasErrors(['address' => 'required'])
            ->assertSee(__('New Address'))
            ->assertSee(__('Back to Company'))
            // valid address -> should be inserted, should not have error, should appear in the prediction list
            ->set('address', self::TEST_SCANNED_ADDRESS)
            ->call('insertNew', Shop::DATA_TYPE_ADDRESS, new DataPredictionService())
            ->assertHasNoErrors('address')
            ->assertCount('addressSuggestions', 2)
            // Shouldn't be able to save the address now.
            ->assertSet('allowSaveAddress', false)
            ->assertDontSee(__('New Address'))
            // The selected Address should be the new one.
            ->assertSet('selectedAddress', self::TEST_SCANNED_ADDRESS)
            // Change selectedAddress back to the edit mode.
            ->set('selectedAddress', '')
            // emit the change event.
            ->emit('shop.data.select', Shop::DATA_TYPE_ADDRESS)
            // THe previously added address has to appear in the prediction list.
            ->assertCount('addressSuggestions', 2)
            ->set('address', self::TEST_SCANNED_ADDRESS . ' new')
            // Change selectedAddress back to the edit mode.
            ->set('selectedAddress', self::TEST_SCANNED_ADDRESS)
            // emit the change event.
            ->emit('shop.data.select', Shop::DATA_TYPE_ADDRESS)
            // The address has to be updated.
            ->assertSet('address', self::TEST_SCANNED_ADDRESS);
    }

    public function testShopSelection()
    {
        $company = $this->createCompany();
        $component = Livewire::test(Shop::class, ['scannedName' => self::TEST_SCANNED_NAME, 'scannedAddress' => self::TEST_SCANNED_ADDRESS, 'shopCompany' => $company->id])
            ->assertSet('name', self::TEST_SCANNED_NAME)
            ->assertSet('address', self::TEST_SCANNED_ADDRESS)
            ->assertSet('shopCompany', $company->id)
            ->assertDontSee(__('You have to setup the company to be able to set the shop.'))
            // Save the address first.
            ->call('insertNew', Shop::DATA_TYPE_ADDRESS, new DataPredictionService())
            ->assertHasNoErrors('address')
            ->assertCount('addressSuggestions', 2)
            // Shouldn't be able to save the address now.
            ->assertSet('allowSaveAddress', false)
            ->assertDontSee(__('New Address'))
            // The selected Address should be the new one.
            ->assertSet('selectedAddress', self::TEST_SCANNED_ADDRESS)
            ->assertSee(__('New Shop'))
            ->assertSee(__('Back to Company'))
            ->assertCount('shopSuggestions', 0)
            // invalid value.
            ->set('name', '')
            ->call('insertNew', Shop::DATA_TYPE_SHOP, new DataPredictionService())
            ->assertHasErrors(['name' => 'required'])
            // valid value.
            ->set('name', self::TEST_SCANNED_NAME)
            ->call('insertNew', Shop::DATA_TYPE_SHOP, new DataPredictionService())
            ->assertHasNoErrors('name')
            ->assertCount('shopSuggestions', 1)
            ->assertSet('allowSaveShop', false)
            ->assertDontSee(__('New Address'))
            ->assertDontSee(__('New Shop'))
            ->assertSee(__('Back to Company'))
            ->assertDontSee(__('Setup Items'));
        // find the id of the shop suggestion.
        $shopSuggestion = $component->get('shopSuggestions')[0];
        // set the shop.
        $component->set('selectedShop', $shopSuggestion['id'])
            ->emit('shop.data.select', Shop::DATA_TYPE_SHOP)
            ->assertDontSee(__('New Address'))
            ->assertDontSee(__('New Shop'))
            ->assertSee(__('Back to Company'))
            ->assertSee(__('Setup Items'))
            ->set('selectedShop', '')
            ->emit('shop.data.select', Shop::DATA_TYPE_SHOP)
            ->assertCount('shopSuggestions', 1)
            ->set('name', self::TEST_SCANNED_NAME . ' new')
            ->set('selectedShop', $shopSuggestion['id'])
            ->emit('shop.data.select', Shop::DATA_TYPE_SHOP);
        // Submit the form.
        $component->call('validateInputs')
            ->assertHasNoErrors(['taxNumber' => 'required'])
            ->assertHasNoErrors(['name' => 'required'])
            ->assertEmitted('basket.data.update');
    }

    private function createCompany()
    {
        // Create addresses one for the company and one for the shop.
        $companyAddress = (new Address())->firstOrCreate([
            'raw' => 'TestCompanyAddress',
        ]);
        return (new Company())->firstOrCreate([
            'name' => 'TestCompany',
            'address_id' => $companyAddress->id,
            'tax_number' => '12345678910',
        ]);
    }
}
