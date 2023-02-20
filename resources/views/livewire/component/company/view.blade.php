<div class="card">
    <h4 class="card-header"><i class="bi bi-eyeglasses me-3"></i>{{ __('View') }}</h4>
    <div class="card-body">
        <p class="card-text fw-bold ms-2 fs-3">{{ $contentParameters['company']['companyName'] }}</p>
        <p class="card-text fw-bold ms-2 fs-3">{{ $contentParameters['company']['companyTaxNumber'] }}</p>
        @php
            $addressRaw = '';
            foreach ($contentParameters['addresses'] as $address) {
                if ($address['id'] == $contentParameters['company']['companyAddress']) {
                    $addressRaw = $address['raw'];
                    break;
                }
            }
        @endphp
        <p class="card-text fw-bold ms-2 fs-3">{{ $addressRaw }}</p>
        <div class="d-flex flex-row-reverse">
            <button type="button" class="btn btn-primary" wire:click="$emitUp('company.update', {'id': @this.contentParameters.company.id})">{{ __('Update') }}</button>
        </div>
    </div>
</div>
