<div>
    @include('livewire.component.offcanvasform.textinput', ['modelId' => 'name', 'formLabel' => __('Company Name')])
    @include('livewire.component.offcanvasform.textinput', ['modelId' => 'taxNumber', 'formLabel' => __('Tax Number')])
    @include('livewire.component.offcanvasform.textinput', ['modelId' => 'address', 'formLabel' => __('Address')])
    @if ($allowSaveAddress)
        <button wire:click="insertNew('{{ self::DATA_TYPE_ADDRESS }}')" class="btn btn-primary">{{ __('New Address') }}</button>
    @endif
    @if ($allowSaveCompany)
        <button wire:click="insertNew('{{ self::DATA_TYPE_COMPANY }}')" class="btn btn-primary">{{ __('New Company') }}</button>
    @endif
    @if (!$allowSaveCompany && !$allowSaveAddress)
        <button wire:click="$emitUp('basket.data.update', 'companyId', @this.companySuggestions[0]['id'])" class="btn btn-primary">{{ __('Next') }}</button>
    @endif
    <hr>
    <div class="mb-3">
        <label for="selectedAddress" class="form-label">{{ __('Address') }}</label>
        <select class="form-select" id="selectedAddress" wire:model="selectedAddress" wire:change='$emitSelf("company.data.select", "{{ self::DATA_TYPE_ADDRESS }}")'>
            <option value="{{ $scannedAddress }}" >{{ __('Select Address') }}</option>
            @foreach ($addressSuggestions as $addressSuggestion)
                <option value="{{ $addressSuggestion['raw'] }}"
                    @if($addressSuggestion['raw'] == $selectedAddress) selected @endif>{{ $addressSuggestion['raw'] }} ({{ $addressSuggestion['percentage'] }}%)</option>
            @endforeach
        </select>
    </div>
    <hr>
    <div class="mb-3">
        <label for="selectedCompany" class="form-label">{{ __('Company') }}</label>
        <select class="form-select" wire:model="selectedCompany" id="selectedCompany" wire:change='$emitSelf("company.data.select", "{{ self::DATA_TYPE_COMPANY }}")'>
            <option value="" @if($selectedCompany == "") selected @endif>{{ __('Select Company') }}</option>
            @foreach ($companySuggestions as $company)
                <option value="{{ $company['tax_number'] }}" @if($selectedCompany == $company['tax_number']) selected @endif>{{ $company['name'] }} ({{ $company['percentage'] }}%)</option>
            @endforeach
        </select>
    </div>
</div>