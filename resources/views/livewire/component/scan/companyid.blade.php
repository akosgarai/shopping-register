<div>
    <livewire:component.scan.company
        :scannedName="array_key_exists('companyName', $basket) ? $basket['companyName'] : ''"
        :scannedAddress="array_key_exists('companyAddress', $basket) ? $basket['companyAddress'] : ''"
        :scannedTaxNumber="array_key_exists('taxNumber', $basket) ? $basket['taxNumber'] : ''"
    >
</div>
