<div class="card">
    <h4 class="card-header"><i class="bi bi-pencil-square me-3"></i>{{ __('Update') }}</h4>
    <div class="card-body">
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.address.raw', 'formLabel' => __('Address')])
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.address.createdAt', 'formLabel' => __('Created'), 'readonly' => true])
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.address.updatedAt', 'formLabel' => __('Updated'), 'readonly' => true])
        <div class="d-flex flex-row-reverse">
            <button type="button" class="btn btn-primary" wire:click="$emitUp('address.update', {'raw': @this.contentParameters.address.raw})">{{ __('Update') }}</button>
        </div>
    </div>
</div>
