<div class="card">
    <h4 class="card-header"><i class="bi bi-plus-circle me-3"></i>{{ __('Create') }}</h4>
    <div class="card-body">
        @include('livewire.component.offcanvasform.textinput', ['modelId' => 'contentParameters.item.itemName', 'formLabel' => __('Item Name')])
        @if ($contentParameters['item']['itemName'] != '')
            <div class="d-flex flex-row-reverse">
                <button type="button" class="btn btn-primary" wire:click="$emitUp('item.create', {'itemName': @this.contentParameters.item.itemName})">{{ __('Save') }}</button>
            </div>
        @endif
    </div>
</div>
