<div class="card">
    <h4 class="card-header"><i class="bi bi-eyeglasses me-3"></i>{{ __('View') }}</h4>
    <div class="card-body">
        <p class="card-text fw-bold ms-2 fs-3">{{ $contentParameters['item']['itemName'] }}</p>
        <div class="d-flex flex-row-reverse">
            <button type="button" class="btn btn-primary" wire:click="$emitUp('item.update', {'id': @this.contentParameters.item.id})">{{ __('Update') }}</button>
        </div>
    </div>
</div>
