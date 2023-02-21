<div class="card">
    <h4 class="card-header"><i class="bi bi-eyeglasses me-3"></i>{{ __('View') }}</h4>
    <div class="card-body">
        <p class="card-text fw-bold ms-2 fs-3">{{ $contentParameters['shop']['shopName'] }}</p>
        @php
            $addressRaw = '';
            foreach ($contentParameters['addresses'] as $address) {
                if ($address['id'] == $contentParameters['shop']['shopAddress']) {
                    $addressRaw = $address['raw'];
                    break;
                }
            }
            $companyName = '';
            foreach ($contentParameters['companies'] as $company) {
                if ($company['id'] == $contentParameters['shop']['shopCompany']) {
                    $companyName = $company['name'];
                    break;
                }
            }
        @endphp
        <p class="card-text fw-bold ms-2 fs-3">{{ $addressRaw }}</p>
        <p class="card-text fw-bold ms-2 fs-3">{{ $companyName }}</p>
        <div class="d-flex flex-row-reverse">
            <button type="button" class="btn btn-primary" wire:click="$emitUp('shop.update', @this.contentParameters.shop)">{{ __('Update') }}</button>
        </div>
    </div>
</div>
