<div class="mb-3" id="basketItemTemplate-{{ $templateIndex }}" wire:key="basketItemTemplate-{{ $templateIndex }}">
    <div class="input-group">
        <select class="form-select" wire:model="{{ $itemModelId }}" id="{{ $itemModelId }}">
            <option value="" @if($selected == "") selected @endif>{{ __('Select Item') }}</option>
            @foreach ($options as $item)
                <option value="{{ $item['id'] }}" @if($selected == $item['id']) selected @endif>{{ $item['name'] }}</option>
            @endforeach
        </select>
        <input type="number" class="form-control" id="{{ $priceModelId }}" step="0.01" wire:model="{{ $priceModelId }}"
            wire:change="$set('{{ $unitPriceModelId }}',
            @if($templateIndex == 'new')
                @this.{{ $priceModelId }} / @this.{{ $quantityModelId }}
            @else
                @this.{{ $keyNameBase }}[{{ $templateIndex}}].price / @this.{{ $keyNameBase }}[{{ $templateIndex}}].quantity
            @endif
                )">
        <button type="button" class="btn btn-outline-secondary"
            wire:click="$emitUp('{{ $eventName }}'
                @foreach($eventParameters as $parameter), @this.{{ $parameter }} @endforeach
                @foreach($staticEventParameters as $parameter), {{ $parameter }} @endforeach)">{{ $buttonLabel }}</button>
    </div>
    <span id="errors-{{ $itemModelId }}" class="text-danger" >@error($itemModelId) {{ $message }} @enderror</span>
    <span id="errors-{{ $priceModelId }}" class="text-danger" >@error($priceModelId) {{ $message }} @enderror</span>
    <div class="input-group">
        <input type="number" class="form-control" step="0.01"  id="{{ $quantityModelId }}" wire:model="{{ $quantityModelId }}"
            wire:change="$set('{{ $unitPriceModelId }}',
            @if($templateIndex == 'new')
                @this.{{ $priceModelId }} / @this.{{ $quantityModelId }}
            @else
                @this.{{ $keyNameBase }}[{{ $templateIndex}}].price / @this.{{ $keyNameBase }}[{{ $templateIndex}}].quantity
            @endif
                )">
        <select class="form-select" id="{{ $quantityUnitModelId }}" wire:model="{{ $quantityUnitModelId }}">
            @foreach ($quantityUnits as $unit)
                <option value="{{ $unit['id'] }}" @if($unit['id'] == $selectedQuantityUnit) selected @endif >{{ $unit['name'] }}</option>
            @endforeach
        </select>
        <input type="number" class="form-control" id="{{ $unitPriceModelId }}" wire:model="{{ $unitPriceModelId }}" readonly>
    </div>
</div>
