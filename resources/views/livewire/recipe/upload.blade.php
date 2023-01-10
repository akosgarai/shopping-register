<div class="row">
    @if ($uploadedImageUrl !== '')
        <div class="col-6">
            <img src="{{ $uploadedImageUrl }}" alt="Uploaded image" style="max-height: 400px;">
        </div>
    @endif
    @if ($parsedText !== '')
        <div class="col-6">
            <pre>
                    {{ $parsedText }}
            </pre>
        </div>
    @endif
    @if ($showEditor)
        <div class="col-12">
            <div class="mb-3">
                <label for="recipeName" class="form-label">{{ __('Name') }}</label>
                <input type="text" class="form-control" wire:model="receipt.name" id="recipeName">
            </div>
            <div class="mb-3">
                <label for="recipeAddress" class="form-label">{{ __('Address') }}</label>
                <input type="text" class="form-control" wire:model="receipt.address" id="recipeAddress">
            </div>
            <div class="mb-3">
                <label for="recipeTaxNumber" class="form-label">{{ __('Tax number') }}</label>
                <input type="text" class="form-control" wire:model="receipt.taxNumber" id="recipeTaxNumber">
            </div>
            <!-- loop over receipt.items -->
            @foreach ($receipt['items'] as $index => $item)
                <div class="input-group mb-3">
                    <input type="text" class="form-control" wire:model="receipt.items.{{ $index }}.name">
                    <input type="text" class="form-control" wire:model="receipt.items.{{ $index }}.price">
                    <span class="input-group-text">{{ __('Ft') }}</span>
                </div>
            @endforeach
            <div class="mb-3">
                <label for="recipeSum" class="form-label">{{ __('Total') }}</label>
                <input type="text" class="form-control" wire:model="receipt.total" id="recipeSum">
            </div>
            <div class="mb-3">
                <label for="recipeId" class="form-label">{{ __('ID') }}</label>
                <input type="text" class="form-control" wire:model="receipt.id" id="recipeId">
            </div>
            <div class="mb-3">
                <label for="recipeDate" class="form-label">{{ __('Date') }}</label>
                <input type="text" class="form-control" wire:model="receipt.date" id="recipeDate">
            </div>
        </div>
    @endif
</div>
