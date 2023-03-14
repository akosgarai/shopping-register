<div>
    @if(!$date)
        <h3>{{ __('You have to setup the basket to be able to set the company.') }}</h3>
    @else
    <form wire:submit.prevent="validateInputs">
        @include('livewire.component.forms.textinput', ['modelId' => 'name', 'formLabel' => __('Company Name')])
        @include('livewire.component.forms.textinput', ['modelId' => 'taxNumber', 'formLabel' => __('Tax Number')])
        @include('livewire.component.forms.textinput', ['modelId' => 'address', 'formLabel' => __('Address')])
        <div class="d-flex flex-row-reverse">
            @if ($allowSaveAddress)
                <button wire:click.prevent="insertNew('{{ self::DATA_TYPE_ADDRESS }}')" class="btn btn-primary">{{ __('New Address') }}</button>
            @endif
            @if ($allowSaveCompany)
                <button wire:click.prevent="insertNew('{{ self::DATA_TYPE_COMPANY }}')" class="btn btn-primary">{{ __('New Company') }}</button>
            @endif
            @if (!$allowSaveCompany && !$allowSaveAddress && $selectedCompany != "")
                <button type="submit" class="btn btn-success">{{ __('Setup Shop') }}</button>
            @endif
            <input type="button" wire:click="$emitUp('action.back');" class="btn btn-info me-auto" value="{{ __('Back to Basket ID') }}">
        </div>
    </form>
    <hr>
    <div class="mb-3">
        <label for="selectedAddress" class="form-label">{{ __('Address') }}</label>
        <select class="form-select" id="selectedAddress" wire:model="selectedAddress" wire:change='$emitSelf("company.data.select", "{{ self::DATA_TYPE_ADDRESS }}")'>
            <option value="{{ $scannedAddress }}" @if($selectedAddress == '') selected @endif>{{ __('Select Address') }}</option>
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
    @endif
</div>
