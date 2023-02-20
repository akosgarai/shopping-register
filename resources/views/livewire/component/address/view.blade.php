<div class="card">
    <h4 class="card-header"><i class="bi bi-eyeglasses me-3"></i>{{ __('View') }}</h4>
    <div class="card-body">
        <p class="card-text fw-bold ms-2 fs-3">{{ $contentParameters['address']['raw'] }}</p>
        <div class="d-flex flex-row-reverse">
            <button type="button" class="btn btn-primary" wire:click="$emitUp('address.update', {'id': @this.contentParameters.address.id})">{{ __('Update') }}</button>
        </div>
    </div>
</div>
