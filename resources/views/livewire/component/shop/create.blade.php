<div class="card">
    <h4 class="card-header"><i class="bi bi-plus-circle me-3"></i>{{ __('Create') }}</h4>
    <div class="card-body">
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.shop.shopName', 'formLabel' => __('Shop Name')])
        @include('livewire.component.offcanvasform.selectorcompany', [
            'modelId' => 'contentParameters.shop.shopCompany',
            'companies' => $contentParameters['companies'],
            'selected' => $contentParameters['shop']['shopCompany']
        ])
        @include('livewire.component.offcanvasform.selectoraddress', [
            'modelId' => 'contentParameters.shop.shopAddress',
            'addresses' => $contentParameters['addresses'],
            'selected' => $contentParameters['shop']['shopAddress']
        ])
        @if ($contentParameters['shop']['shopName'] != '' && $contentParameters['shop']['shopCompany'] != '' && $contentParameters['shop']['shopAddress'] != '')
            <div class="d-flex flex-row-reverse">
                <button type="button" class="btn btn-primary" wire:click="$emitUp('shop.create', @this.contentParameters.shop)">{{ __('Save') }}</button>
            </div>
        @endif
    </div>
</div>
