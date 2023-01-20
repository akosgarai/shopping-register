<div class="mb-3">
    <label for="{{ $modelId }}" class="form-label">{{ __('Company') }}</label>
    <select class="form-select" wire:model="{{ $modelId }}" id="{{ $modelId }}">
        <option value="" @if($selected == "") selected @endif>{{ __('Select Company') }}</option>
        @foreach ($companies as $company)
            <option value="{{ $company['id'] }}" @if($selected == $company['id']) selected @endif>{{ $company['name'] }}</option>
        @endforeach
    </select>
    <span id="errors-{{ $modelId }}" class="text-danger" style="display: none;"></span>
</div>
