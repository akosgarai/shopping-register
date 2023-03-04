<div class="mb-3" id="basketItemTemplate-{{ $templateIndex }}" wire:key="basketItemTemplate-{{ $templateIndex }}">
    <div class="input-group">
        <select class="form-select" wire:model="{{ $itemModelId }}" id="{{ $itemModelId }}">
            <option value="" @if($selected == "") selected @endif>{{ __('Select Item') }}</option>
            @foreach ($options as $item)
                <option value="{{ $item['id'] }}" @if($selected == $item['id']) selected @endif>{{ $item['name'] }}</option>
            @endforeach
        </select>
        <input type="number" class="form-control" id="{{ $priceModelId }}" step="0.01" wire:model="{{ $priceModelId }}">
        <button type="button" class="btn btn-outline-secondary"
            wire:click="$emitUp('{{ $eventName }}'
                @foreach($eventParameters as $parameter), @this.{{ $parameter }} @endforeach
                @foreach($staticEventParameters as $parameter), {{ $parameter }} @endforeach)">{{ $buttonLabel }}</button>
    </div>
    <span id="errors-{{ $itemModelId }}" class="text-danger" >@error($itemModelId) {{ $message }} @enderror</span>
    <span id="errors-{{ $priceModelId }}" class="text-danger" >@error($priceModelId) {{ $message }} @enderror</span>
</div>
