<div class="mb-3">
    <label for="{{ $modelId }}" class="form-label">{{ __('Shop') }}</label>
    <select class="form-select" wire:model="{{ $modelId }}" id="{{ $modelId }}">
        <option value="" @if($selected == "") selected @endif>{{ __('Select Shop') }}</option>
        @foreach ($shops as $shop)
            <option value="{{ $shop['id'] }}" @if($selected == $shop['id']) selected @endif>{{ $shop['name'] }}, {{ $shop['address']['raw'] }}</option>
        @endforeach
    </select>
    <span id="errors-{{ $modelId }}" class="text-danger" >@error($modelId) {{ $message }} @enderror</span>
</div>
