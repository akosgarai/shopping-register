<div class="card">
    <h4 class="card-header"><i class="bi bi-trash me-3"></i>{{ __('Delete') }}</h4>
    <div class="card-body">
        <h5 class="card-title mb-2">{{ __('Are you sure you want to delete this shop?') }}</h5>
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
            <button type="button" class="btn btn-danger" wire:click="$emitUp('shop.delete', @this.contentParameters.shop.id)">{{ __('Delete') }}</button>
        </div>
    </div>
</div>
