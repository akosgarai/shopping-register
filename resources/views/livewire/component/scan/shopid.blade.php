<div>
    <livewire:component.scan.shop
        :scannedName="array_key_exists('marketName', $basket) ? $basket['marketName'] : ''"
        :scannedAddress="array_key_exists('marketAddress', $basket) ? $basket['marketAddress'] : ''"
        :shopCompany="array_key_exists('company_id', $basket) ? $basket['company_id'] : ''"
    >
</div>
