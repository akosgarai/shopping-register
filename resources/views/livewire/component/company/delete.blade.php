<div class="card">
    <h4 class="card-header"><i class="bi bi-trash me-3"></i>{{ __('Delete') }}</h4>
    <div class="card-body">
        <h5 class="card-title mb-2">{{ __('Are you sure you want to delete this company?') }}</h5>
        <p class="card-text fw-bold ms-2 fs-3">{{ $contentParameters['company']['companyName'] }}</p>
        <p class="card-text fw-bold ms-2 fs-3">{{ $contentParameters['company']['companyTaxNumber'] }}</p>
        <div class="d-flex flex-row-reverse">
            <button type="button" class="btn btn-danger" wire:click="$emitUp('company.delete', {'id': @this.contentParameters.company.id})">{{ __('Delete') }}</button>
        </div>
    </div>
</div>
