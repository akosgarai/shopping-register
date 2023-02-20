<div class="card">
    <h4 class="card-header"><i class="bi bi-pencil-square me-3"></i>{{ __('Update') }}</h4>
    <div class="card-body">
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.company.companyName', 'formLabel' => __('Company Name')])
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.company.companyTaxNumber', 'formLabel' => __('Tax Number')])
        @include('livewire.component.offcanvasform.selectoraddress', [
            'modelId' => 'contentParameters.company.companyAddress',
            'addresses' => $contentParameters['addresses'],
            'selected' => $contentParameters['company']['companyAddress']
        ])
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.company.createdAt', 'formLabel' => __('Created'), 'readonly' => true])
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.company.updatedAt', 'formLabel' => __('Updated'), 'readonly' => true])
        @if ($contentParameters['company']['companyName'] != '' && $contentParameters['company']['companyTaxNumber'] != '' && $contentParameters['company']['companyAddress'] != '')
            <div class="d-flex flex-row-reverse">
                <button type="button" class="btn btn-primary" wire:click="$emitUp('company.update', {'companyName': @this.contentParameters.company.companyName, 'companyTaxNumber': @this.contentParameters.company.companyTaxNumber, 'companyAddress': @this.contentParameters.company.companyAddress})">{{ __('Save') }}</button>
            </div>
        @endif
    </div>
</div>
