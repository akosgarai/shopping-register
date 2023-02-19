<div>
    <livewire:component.scan.basket-item
        :scannedTotal="array_key_exists('total', $basket) ? $basket['total'] : ''"
        :scannedItems="array_key_exists('items', $basket) ? $basket['items'] : []"
        :shopId="array_key_exists('marketId', $basket) ? $basket['marketId'] : ''"
    >
</div>
