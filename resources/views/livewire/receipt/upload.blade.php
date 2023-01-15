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
                <label for="receiptName" class="form-label">{{ __('Company name') }}</label>
                <input type="text" class="form-control" wire:model="receipt.companyName" id="receiptName">
            </div>
            <div class="mb-3">
                <label for="receiptAddress" class="form-label">{{ __('Company address') }}</label>
                <input type="text" class="form-control" wire:model="receipt.companyAddress" id="receiptAddress">
            </div>
            <div class="mb-3">
                <label for="receiptTaxNumber" class="form-label">{{ __('Tax number') }}</label>
                <input type="text" class="form-control" wire:model="receipt.taxNumber" id="receiptTaxNumber">
            </div>
            <div class="mb-3">
                <label for="receiptName" class="form-label">{{ __('Market name') }}</label>
                <input type="text" class="form-control" wire:model="receipt.marketName" id="receiptName">
            </div>
            <div class="mb-3">
                <label for="receiptAddress" class="form-label">{{ __('Market address') }}</label>
                <input type="text" class="form-control" wire:model="receipt.marketAddress" id="receiptAddress">
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
                <label for="receiptSum" class="form-label">{{ __('Total') }}</label>
                <input type="text" class="form-control" wire:model="receipt.total" id="receiptSum">
            </div>
            <div class="mb-3">
                <label for="receiptId" class="form-label">{{ __('ID') }}</label>
                <input type="text" class="form-control" wire:model="receipt.id" id="receiptId">
            </div>
            <div class="mb-3">
                <label for="receiptDate" class="form-label">{{ __('Date') }}</label>
                <input type="text" class="form-control" wire:model="receipt.date" id="receiptDate">
            </div>
        </div>
    @endif
</div>
