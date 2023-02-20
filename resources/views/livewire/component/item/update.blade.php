<div class="card">
    <h4 class="card-header"><i class="bi bi-pencil-square me-3"></i>{{ __('Update') }}</h4>
    <div class="card-body">
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.item.itemName', 'formLabel' => __('Item Name')])
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.item.createdAt', 'formLabel' => __('Created'), 'readonly' => true])
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.item.updatedAt', 'formLabel' => __('Updated'), 'readonly' => true])
        <div class="d-flex flex-row-reverse">
            <button type="button" class="btn btn-primary" wire:click="$emitUp('item.update', {'itemName': @this.contentParameters.item.itemName})">{{ __('Update') }}</button>
        </div>
    </div>
</div>
