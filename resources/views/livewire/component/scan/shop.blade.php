<div>
    @if(!$shopCompany)
        <h3>{{ __('You have to setup the company to be able to set the shop.') }}</h3>
    @else
        @include('livewire.component.forms.textinput', ['modelId' => 'name', 'formLabel' => __('Shop Name')])
        @include('livewire.component.forms.textinput', ['modelId' => 'address', 'formLabel' => __('Address')])
        <div class="d-flex flex-row-reverse">
            @if ($allowSaveAddress)
                <button wire:click="insertNew('{{ self::DATA_TYPE_ADDRESS }}')" class="btn btn-primary">{{ __('New Address') }}</button>
            @endif
            @if ($allowSaveShop)
                <button wire:click="insertNew('{{ self::DATA_TYPE_SHOP }}')" class="btn btn-primary">{{ __('New Shop') }}</button>
            @endif
            @if (!$allowSaveShop && !$allowSaveAddress)
                <button wire:click="$emitUp('basket.data.update', { 'marketId' : '{{ $selectedShop }}' })" class="btn btn-primary">{{ __('Setup Items') }}</button>
            @endif
            <input type="button" wire:click="$emitUp('action.back');" class="btn btn-info me-auto" value="{{ __('Back to Company') }}">
        </div>
        <hr>
        <div class="mb-3">
            <label for="selectedAddress" class="form-label">{{ __('Address') }}</label>
            <select class="form-select" id="selectedAddress" wire:model="selectedAddress" wire:change='$emitSelf("shop.data.select", "{{ self::DATA_TYPE_ADDRESS }}")'>
                <option value="{{ $scannedAddress }}" >{{ __('Select Address') }}</option>
                @foreach ($addressSuggestions as $addressSuggestion)
                    <option value="{{ $addressSuggestion['raw'] }}"
                        @if($addressSuggestion['raw'] == $selectedAddress) selected @endif>{{ $addressSuggestion['raw'] }} ({{ $addressSuggestion['percentage'] }}%)</option>
                @endforeach
            </select>
        </div>
        <hr>
        <div class="mb-3">
            <label for="selectedShop" class="form-label">{{ __('Shop') }}</label>
            <select class="form-select" wire:model="selectedShop" id="selectedShop" wire:change='$emitSelf("shop.data.select", "{{ self::DATA_TYPE_SHOP }}")'>
                <option value="" @if($selectedShop == "") selected @endif>{{ __('Select Shop') }}</option>
                @foreach ($shopSuggestions as $shop)
                    <option value="{{ $shop['id'] }}" @if($selectedShop == $shop['id']) selected @endif>{{ $shop['name'] }} ({{ $shop['percentage'] }}%)</option>
                @endforeach
            </select>
        </div>
    @endif
</div>
