<?php

namespace Tests\Feature\Livewire\Component\Scan;

use App\Http\Livewire\Component\Scan\Basket;
use App\Models\Address;
use App\Models\Basket as BasketModel;
use App\Models\Company;
use App\Models\Shop;
use App\Models\User;

use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BasketTest extends TestCase
{
    use RefreshDatabase;

    const TEST_SCANNED_BASKET_ID = 'TestBasketId';
    const TEST_SCANNED_DATE = '2020-01-01T12:00';

    private User $user;

    /** @test */
    public function testTheComponentCanRender()
    {
        $this->actAsUser();
        Livewire::test(Basket::class)->assertStatus(200);
    }

    public function testValidInitialParameters()
    {
        $this->actAsUser();
        Livewire::test(Basket::class, ['scannedBasketId' => self::TEST_SCANNED_BASKET_ID, 'scannedBasketDate' => self::TEST_SCANNED_DATE])
            ->assertSet('basketId', self::TEST_SCANNED_BASKET_ID)
            ->assertSet('basketDate', self::TEST_SCANNED_DATE)
            ->assertCount('suggestions', 0)
            ->assertSet('basketPreview', null)
            ->assertSee(__('Setup Company'))
            ->assertStatus(200);
    }

    public function testInvalidInitialParameters()
    {
        $this->actAsUser();
        $emptyValue = '';
        Livewire::test(Basket::class, ['scannedBasketId' => $emptyValue, 'scannedBasketDate' => $emptyValue])
            ->assertSet('basketId', $emptyValue)
            ->assertSet('basketDate', $emptyValue)
            ->assertCount('suggestions', 0)
            ->assertSet('basketPreview', null)
            ->assertDontSee(__('Setup Company'))
            ->assertStatus(200);
    }

    public function testValidateInputParameters()
    {
        $this->actAsUser();
        $emptyValue = '';
        Livewire::test(Basket::class, ['scannedBasketId' => $emptyValue, 'scannedBasketDate' => $emptyValue])
            ->assertSet('basketId', $emptyValue)
            ->assertSet('basketDate', $emptyValue)
            ->assertCount('suggestions', 0)
            ->assertSet('basketPreview', null)
            ->assertDontSee(__('Setup Company'))
            ->call('validateInputs')
            ->assertHasErrors(['basketId', 'basketDate'])
            ->set('basketId', self::TEST_SCANNED_BASKET_ID)
            ->call('validateInputs')
            ->assertHasErrors(['basketDate'])
            ->set('basketDate', self::TEST_SCANNED_DATE)
            ->call('validateInputs')
            ->assertEmitted('basket.data.update')
            ->assertHasNoErrors(['basketId', 'basketDate'])
        ;
    }

    public function testListenToEvent()
    {
        $this->actAsUser();
        $updatedId = 'UpdatedId';
        $updatedDate = '2022-01-01T12:00';
        Livewire::test(Basket::class, ['scannedBasketId' => self::TEST_SCANNED_BASKET_ID, 'scannedBasketDate' => self::TEST_SCANNED_DATE])
            ->assertSet('basketId', self::TEST_SCANNED_BASKET_ID)
            ->assertSet('basketDate', self::TEST_SCANNED_DATE)
            ->assertCount('suggestions', 0)
            ->assertSet('basketPreview', null)
            ->assertSee(__('Setup Company'))
            // Emit data change event. update both value.
            ->emit('basket.data.extracted', [
                'basketId' => $updatedId,
                'date' => $updatedDate,
            ])
            ->assertSet('basketId', $updatedId)
            ->assertSet('basketDate', $updatedDate)
            ->assertStatus(200);
    }

    public function testPreview()
    {
        $this->actAsUser();
        $basket = $this->createBasket();
        Livewire::test(Basket::class, ['scannedBasketId' => self::TEST_SCANNED_BASKET_ID, 'scannedBasketDate' => self::TEST_SCANNED_DATE])
            ->assertSet('basketId', self::TEST_SCANNED_BASKET_ID)
            ->assertSet('basketDate', self::TEST_SCANNED_DATE)
            ->assertCount('suggestions', 1)
            ->assertSet('basketPreview', null)
            ->assertSee(__('Setup Company'))
            ->assertSee(__('Similar Baskets'))
            ->assertSeeHtml('<a href="#" wire:click.prevent="selectPreview( 0 )" >'.$basket->receipt_id.'</a> (100%)</span>')
            ->call('selectPreview', 0)
            ->assertSet('basketPreview.receipt_id', $basket->receipt_id)
        ;


    }

    private function actAsUser()
    {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    private function createBasket()
    {
        // Create addresses one for the company and one for the shop.
        $companyAddress = (new Address())->firstOrCreate([
            'raw' => 'TestCompanyAddress',
        ]);
        $shopAddress = (new Address())->firstOrCreate([
            'raw' => 'TestShopAddress',
        ]);
        // Create a company.
        $company = (new Company())->firstOrCreate([
            'name' => 'TestCompany',
            'address_id' => $companyAddress->id,
            'tax_number' => '12345678910',
        ]);
        // Create a shop.
        $shop = (new Shop())->firstOrCreate([
            'name' => 'TestShop',
            'address_id' => $shopAddress->id,
            'company_id' => $company->id,
        ]);
        // Create a basket.
        return (new BasketModel())->firstOrCreate([
            'receipt_id' => self::TEST_SCANNED_BASKET_ID,
            'date' => self::TEST_SCANNED_DATE,
            'shop_id' => $shop->id,
            'total' => 100,
            'user_id' => auth()->user()->id,
        ]);
    }
}
