<div class="card">
    <h4 class="card-header"><i class="bi bi-pencil-square me-3"></i>{{ __('Update') }}</h4>
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
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.shop.createdAt', 'formLabel' => __('Created'), 'readonly' => true])
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.shop.updatedAt', 'formLabel' => __('Updated'), 'readonly' => true])
        @if ($contentParameters['shop']['shopName'] != '' && $contentParameters['shop']['shopCompany'] != '' && $contentParameters['shop']['shopAddress'] != '')
            <div class="d-flex flex-row-reverse">
                <button type="button" class="btn btn-primary" wire:click="$emitUp('shop.update', @this.contentParameters.shop)">{{ __('Save') }}</button>
            </div>
        @endif
    </div>
</div>
