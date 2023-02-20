<div class="card">
    <h4 class="card-header"><i class="bi bi-plus-circle me-3"></i>{{ __('Create') }}</h4>
    <div class="card-body">
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.address.raw', 'formLabel' => __('Address')])
        @if ($contentParameters['address']['raw'] != '')
            <div class="d-flex flex-row-reverse">
                <button type="button" class="btn btn-primary" wire:click="$emitUp('address.create', {'raw': @this.contentParameters.address.raw})">{{ __('Save') }}</button>
            </div>
        @endif
    </div>
</div>
