<?php

namespace Tests\Feature\Livewire\Component\Scan;

use App\Http\Livewire\Component\Scan\Company;
use App\Services\DataPredictionService;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    const TEST_SCANNED_NAME = 'TestName';
    const TEST_SCANNED_ADDRESS = 'TestAddress';
    const TEST_SCANNED_TAX_NUMBER = '12345678910';
    const TEST_SCANNED_DATE = '2020-01-01 12:00';

    /** @test */
    public function testTheComponentCanRender()
    {
        Livewire::test(Company::class)->assertStatus(200);
    }

    public function testTheInitialParametersWithoutDate()
    {
        Livewire::test(Company::class, ['scannedName' => self::TEST_SCANNED_NAME, 'scannedAddress' => self::TEST_SCANNED_ADDRESS, 'scannedTaxNumber' => self::TEST_SCANNED_TAX_NUMBER])
            ->assertSet('name', self::TEST_SCANNED_NAME)
            ->assertSet('address', self::TEST_SCANNED_ADDRESS)
            ->assertSet('taxNumber', self::TEST_SCANNED_TAX_NUMBER)
            ->assertSet('date', '')
            ->assertSee(__('You have to setup the basket to be able to set the company.'));
    }

    public function testTheInitialParametersWithDate()
    {
        Livewire::test(Company::class, [
                'scannedName' => self::TEST_SCANNED_NAME,
                'scannedAddress' => self::TEST_SCANNED_ADDRESS,
                'scannedTaxNumber' => self::TEST_SCANNED_TAX_NUMBER,
                'date' => self::TEST_SCANNED_DATE
            ])
            ->assertSet('name', self::TEST_SCANNED_NAME)
            ->assertSet('address', self::TEST_SCANNED_ADDRESS)
            ->assertSet('taxNumber', self::TEST_SCANNED_TAX_NUMBER)
            ->assertSet('date', self::TEST_SCANNED_DATE)
            ->assertDontSee(__('You have to setup the basket to be able to set the company.'));
    }

    public function testBasketDataHandler()
    {
        Livewire::test(Company::class, [])
            ->assertSet('name', '')
            ->assertSet('address', '')
            ->assertSet('taxNumber', '')
            ->assertSet('date', '')
            ->assertSee(__('You have to setup the basket to be able to set the company.'))
            ->emit('basket.data.extracted', [
                'companyName' => self::TEST_SCANNED_NAME,
                'companyAddress' => self::TEST_SCANNED_ADDRESS,
                'taxNumber' => self::TEST_SCANNED_TAX_NUMBER,
                'date' => self::TEST_SCANNED_DATE
            ])
            ->assertSet('name', self::TEST_SCANNED_NAME)
            ->assertSet('address', self::TEST_SCANNED_ADDRESS)
            ->assertSet('taxNumber', self::TEST_SCANNED_TAX_NUMBER)
            ->assertSet('date', self::TEST_SCANNED_DATE)
            ->assertDontSee(__('You have to setup the basket to be able to set the company.'));
    }

    public function testAddressSelection()
    {
        $emptyAddress = '';
        Livewire::test(Company::class, [
                'scannedName' => self::TEST_SCANNED_NAME,
                'scannedAddress' => $emptyAddress,
                'scannedTaxNumber' => self::TEST_SCANNED_TAX_NUMBER,
                'date' => self::TEST_SCANNED_DATE
            ])
            ->assertSet('name', self::TEST_SCANNED_NAME)
            ->assertSet('address', $emptyAddress)
            ->assertSet('taxNumber', self::TEST_SCANNED_TAX_NUMBER)
            ->assertSet('date', self::TEST_SCANNED_DATE)
            // no suggestion due to the empty database.
            ->assertCount('addressSuggestions', 0)
            // Should be able to save the address even with empty address.
            ->assertSet('allowSaveAddress', true)
            ->assertSee(__('New Address'))
            ->assertSee(__('Back to Basket ID'))
            // Save company shouldn't be visible
            ->assertSet('allowSaveCompany', false)
            ->assertDontSee(__('New company'))
            // invalid address (required) -> should not be inserted, should have error
            ->call('insertNew', Company::DATA_TYPE_ADDRESS)
            ->assertHasErrors(['address' => 'required'])
            ->assertSee(__('Back to Basket ID'))
            // valid address -> should be inserted, should not have error, should appear in the prediction list
            ->set('address', self::TEST_SCANNED_ADDRESS)
            ->call('insertNew', Company::DATA_TYPE_ADDRESS, new DataPredictionService())
            ->assertHasNoErrors(['address' => 'required'])
            ->assertCount('addressSuggestions', 1)
            // Shouldn't be able to save the address now.
            ->assertSet('allowSaveAddress', false)
            ->assertDontSee(__('New Address'))
            // The selected Address should be the new one.
            ->assertSet('selectedAddress', self::TEST_SCANNED_ADDRESS)
            // Save company should be visible
            ->assertSet('allowSaveCompany', true)
            ->assertSee(__('New Company'))
            ->assertSee(__('Back to Basket ID'))
            // Change selectedAddress back to the edit mode.
            ->set('selectedAddress', '')
            // emit the change event.
            ->emit('company.data.select', Company::DATA_TYPE_ADDRESS)
            // THe previously added address has to appear in the prediction list.
            ->assertCount('addressSuggestions', 1)
            ->set('address', self::TEST_SCANNED_ADDRESS . ' new')
            // Change selectedAddress back to the edit mode.
            ->set('selectedAddress', self::TEST_SCANNED_ADDRESS)
            // emit the change event.
            ->emit('company.data.select', Company::DATA_TYPE_ADDRESS)
            // The address has to be updated.
            ->assertSet('address', self::TEST_SCANNED_ADDRESS)
        ;
    }

    public function testCompanySelection()
    {
        Livewire::test(Company::class, [
                'scannedName' => self::TEST_SCANNED_NAME,
                'scannedAddress' => self::TEST_SCANNED_ADDRESS,
                'scannedTaxNumber' => self::TEST_SCANNED_TAX_NUMBER,
                'date' => self::TEST_SCANNED_DATE
            ])
            ->assertSet('name', self::TEST_SCANNED_NAME)
            ->assertSet('address', self::TEST_SCANNED_ADDRESS)
            ->assertSet('taxNumber', self::TEST_SCANNED_TAX_NUMBER)
            ->assertSet('date', self::TEST_SCANNED_DATE)
            // Save the address first.
            ->call('insertNew', Company::DATA_TYPE_ADDRESS, new DataPredictionService())
            ->assertCount('addressSuggestions', 1)
            // Shouldn't be able to save the address now.
            ->assertSet('allowSaveAddress', false)
            ->assertDontSee(__('New Address'))
            // The selected Address should be the new one.
            ->assertSet('selectedAddress', self::TEST_SCANNED_ADDRESS)
            // Save company should be visible
            ->assertSet('allowSaveCompany', true)
            ->assertSee(__('New Company'))
            ->assertSee(__('Back to Basket ID'))
            ->assertCount('companySuggestions', 0)
            // make the company name and the tax number empty.
            ->set('name', '')
            ->set('taxNumber', '')
            // Try to save.
            ->call('insertNew', Company::DATA_TYPE_COMPANY, new DataPredictionService())
            ->assertHasErrors(['name' => 'required'])
            ->assertHasErrors(['taxNumber' => 'required'])
            ->assertHasNoErrors(['address' => 'required'])
            // setup the name then save.
            ->set('name', self::TEST_SCANNED_NAME)
            ->call('insertNew', Company::DATA_TYPE_COMPANY, new DataPredictionService())
            ->assertHasErrors(['taxNumber' => 'required'])
            ->assertHasNoErrors(['name' => 'required'])
            ->assertHasNoErrors(['address' => 'required'])
            // setup the tax number then save.
            ->set('taxNumber', self::TEST_SCANNED_TAX_NUMBER)
            ->call('insertNew', Company::DATA_TYPE_COMPANY, new DataPredictionService())
            ->assertHasNoErrors(['taxNumber' => 'required'])
            ->assertHasNoErrors(['name' => 'required'])
            ->assertHasNoErrors(['address' => 'required'])
            ->assertCount('companySuggestions', 1)
            ->assertSet('allowSaveCompany', false)
            ->assertDontSee(__('New Address'))
            ->assertDontSee(__('New Company'))
            ->assertSee(__('Back to Basket ID'))
            ->assertDontSee(__('Setup Shop'))
            // Now setup the company selector.
            ->set('selectedCompany', self::TEST_SCANNED_TAX_NUMBER)
            ->emit('company.data.select', Company::DATA_TYPE_COMPANY)
            ->assertSet('allowSaveCompany', false)
            ->assertDontSee(__('New Address'))
            ->assertDontSee(__('New Company'))
            ->assertSee(__('Back to Basket ID'))
            ->assertSee(__('Setup Shop'))
            // Change selectedAddress back to the edit mode.
            ->set('selectedCompany', '')
            // emit the change event.
            ->emit('company.data.select', Company::DATA_TYPE_COMPANY)
            // THe previously added company has to appear in the prediction list.
            ->assertCount('companySuggestions', 1)
            ->set('taxNumber', '12345678911')
            // Change selectedAddress back to the edit mode.
            ->set('selectedCompany', self::TEST_SCANNED_TAX_NUMBER)
            // emit the change event.
            ->emit('company.data.select', Company::DATA_TYPE_COMPANY)
            // The address has to be updated.
            ->assertSet('taxNumber', self::TEST_SCANNED_TAX_NUMBER)
            // Submit the form.
            ->call('validateInputs')
            ->assertHasNoErrors(['taxNumber' => 'required'])
            ->assertHasNoErrors(['name' => 'required'])
            ->assertEmitted('basket.data.update');
    }
}
