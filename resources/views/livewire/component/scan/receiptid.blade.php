<div>
    <livewire:component.scan.basket
        :scannedBasketId="array_key_exists('basketId', $basket) ? $basket['basketId'] : ''"
        :scannedBasketDate="array_key_exists('date', $basket) ? $basket['date'] : ''"
        >
</div>
