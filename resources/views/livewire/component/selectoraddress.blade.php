<div class="mb-3">
    <label for="{{ $modelId }}" class="form-label">{{ __('Address') }}</label>
    <select class="form-select" wire:model="{{ $modelId }}" id="{{ $modelId }}">
        <option value="" @if($selected == "") selected @endif>{{ __('Select Address') }}</option>
        @foreach ($addresses as $address)
            <option value="{{ $address['id'] }}" @if($selected == $address['id']) selected @endif>{{ $address['raw'] }}</option>
        @endforeach
    </select>
    <span id="errors-{{ $modelId }}" class="text-danger" style="display: none;"></span>
</div>
