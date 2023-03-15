<?php

namespace Tests\Feature\Livewire\Component\Scan;

use App\Http\Livewire\Component\Scan\BasketItem;
use App\Models\Address;
use App\Models\Company;
use App\Models\Item;
use App\Models\Shop;
use App\Models\QuantityUnit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Livewire;
use Tests\TestCase;

class BasketItemTest extends TestCase
{
    use RefreshDatabase;

    const TEST_SCANNED_TOTAL = '60';
    const TEST_SCANNED_ITEMS = [
        ['name' => 'TestItem1', 'price' => '10', 'quantity' => '1', 'unit_price' => '10', 'quantity_unit_id' => '3', 'suggestions' => []],
        ['name' => 'TestItem2', 'price' => '20', 'quantity' => '2', 'unit_price' => '10', 'quantity_unit_id' => '3', 'suggestions' => []],
        ['name' => 'TestItem3', 'price' => '30', 'quantity' => '3', 'unit_price' => '10', 'quantity_unit_id' => '3', 'suggestions' => []],
    ];
    const EXPECTED_TEST_SCANNED_ITEMS = [
        ['name' => 'TestItem1', 'price' => '10', 'quantity' => '1', 'unit_price' => '10', 'quantity_unit_id' => '3', 'itemId' => '', 'suggestions' => []],
        ['name' => 'TestItem2', 'price' => '20', 'quantity' => '2', 'unit_price' => '10', 'quantity_unit_id' => '3', 'itemId' => '', 'suggestions' => []],
        ['name' => 'TestItem3', 'price' => '30', 'quantity' => '3', 'unit_price' => '10', 'quantity_unit_id' => '3', 'itemId' => '', 'suggestions' => []],
    ];

    private $quantityUnits;
    private Shop $shop;

    /** @test */
    public function testTheComponentCanRender()
    {
        Livewire::test(BasketItem::class)->assertStatus(200);
    }

    public function testTheInitialParametersWithoutShop()
    {
        $this->caseNoShopIdSet();
    }

    public function testUpdateBasketEvent()
    {
        $case = $this->caseNoShopIdSet();
        $case = $this->caseEmitBasketEvent($case);
    }

    public function testInsertNew()
    {
        $case = $this->caseNoShopIdSet();
        $case = $this->caseEmitBasketEvent($case);
        $case->call('insertNew', 0)
            ->assertSet('items.0.name', self::EXPECTED_TEST_SCANNED_ITEMS[0]['name'])
            ->assertSet('items.0.price', self::EXPECTED_TEST_SCANNED_ITEMS[0]['price'])
            ->assertSet('items.0.quantity', self::EXPECTED_TEST_SCANNED_ITEMS[0]['quantity'])
            ->assertSet('items.0.unit_price', self::EXPECTED_TEST_SCANNED_ITEMS[0]['unit_price'])
            ->assertSet('items.0.quantity_unit_id', self::EXPECTED_TEST_SCANNED_ITEMS[0]['quantity_unit_id']);
        // Check that the new item exists in the database.
        $item = (new Item())->where('name', self::EXPECTED_TEST_SCANNED_ITEMS[0]['name'])->first();
        $this->assertNotNull($item);

        $case->assertSet('items.0.itemId', $item->id)
             ->assertCount('items.0.suggestions', 1)
             ->assertSet('items.0.suggestions.0.id', $item->id);
    }
    public function testDeleteItem()
    {
        $case = $this->caseNoShopIdSet();
        $case = $this->caseEmitBasketEvent($case);
        $case->call('deleteItem', 2)
             ->assertCount('items', 2)
             ->assertSet('total', self::TEST_SCANNED_TOTAL - self::EXPECTED_TEST_SCANNED_ITEMS[2]['price'])
             ->call('deleteItem', 1)
             ->assertCount('items', 1)
             ->assertSet('total', self::TEST_SCANNED_TOTAL - self::EXPECTED_TEST_SCANNED_ITEMS[2]['price'] - self::EXPECTED_TEST_SCANNED_ITEMS[1]['price'])
             ->call('deleteItem', 0)
             ->assertCount('items', 0)
             ->assertSet('total', 0)
         ;
    }

    public function testRecalculateTotal()
    {
        $case = $this->caseNoShopIdSet();
        $case = $this->caseEmitBasketEvent($case);
        // Change the quantity of the first item.
        // The total should be recalculated.
        // The item unit price should be recalculated.
        $case->set('items.0.quantity', 3)
             ->set('items.0.price', self::EXPECTED_TEST_SCANNED_ITEMS[0]['price'] * 2)
             ->call('recalculateTotal', 0)
             ->assertSet('items.0.unit_price', self::EXPECTED_TEST_SCANNED_ITEMS[0]['price'] * 2 / 3)
             ->assertSet('total', self::TEST_SCANNED_TOTAL + self::EXPECTED_TEST_SCANNED_ITEMS[0]['price'])
         ;
    }

    public function testAddItem()
    {
        $case = $this->caseNoShopIdSet();
        $case = $this->caseEmitBasketEvent($case);
        for ($i = 0; $i < 10; $i++) {
            $case->call('addItem')
                ->assertCount('items', count(self::EXPECTED_TEST_SCANNED_ITEMS) + $i + 1);
        }
    }

    public function testFinishedSetup()
    {
        $case = $this->caseNoShopIdSet();
        $case = $this->caseEmitBasketEvent($case);
        // Make the second item the first one.
        // Also add one new empty item.
        $case->call('addItem')
             ->set('items.1.name', self::EXPECTED_TEST_SCANNED_ITEMS[0]['name'])
             ->call('insertNew', 0)
             ->call('deleteItem', 3)
             ->call('deleteItem', 2)
             ->call('deleteItem', 1)
             ->call('finishedSetup')
             ->assertSet('total', self::EXPECTED_TEST_SCANNED_ITEMS[0]['price']);
    }

    private function caseNoShopIdSet()
    {
        $this->quantityUnits = (new QuantityUnit())->all();
        return Livewire::test(BasketItem::class, ['scannedTotal' => self::TEST_SCANNED_TOTAL, 'scannedItems' => self::TEST_SCANNED_ITEMS, 'quantityUnits' => $this->quantityUnits, 'shopId' => ''])
            ->assertSet('total', self::TEST_SCANNED_TOTAL)
            ->assertSet('items', self::EXPECTED_TEST_SCANNED_ITEMS)
            ->assertSet('shopId ', '')
            ->assertSet('quantityUnits', $this->quantityUnits)
            ->assertSee(__('You have to setup the Shop first.'));
    }

    private function caseEmitBasketEvent($case)
    {
        $this->shop = $this->createShop();
        $expectedScannedItems = self::EXPECTED_TEST_SCANNED_ITEMS;
        foreach (array_keys($expectedScannedItems) as $key) {
            $expectedScannedItems[$key]['suggestions'] = new Collection();
        }

        return $case->emit('basket.data.extracted', [
            'total' => self::TEST_SCANNED_TOTAL,
            'items' => $expectedScannedItems,
            'marketId' => $this->shop->id,
        ])
            ->assertSet('total', self::TEST_SCANNED_TOTAL)
            ->assertSet('items', $expectedScannedItems)
            ->assertSet('shopId', $this->shop->id)
            ->assertDontSee(__('You have to setup the Shop first.'));
    }
    private function createShop()
    {
        // Create addresses one for the company and one for the shop.
        $companyAddress = (new Address())->firstOrCreate([
            'raw' => 'TestCompanyAddress',
        ]);
        $company = (new Company())->firstOrCreate([
            'name' => 'TestCompany',
            'address_id' => $companyAddress->id,
            'tax_number' => '12345678910',
        ]);
        // Create addresses one for the company and one for the shop.
        $shopAddress = (new Address())->firstOrCreate([
            'raw' => 'TestCompanyAddress',
        ]);
        return (new Shop())->firstOrCreate([
            'name' => 'TestShop',
            'address_id' => $shopAddress->id,
            'company_id' => $company->id,
        ]);
    }
}
