<div>
    @if(!$shopId)
        <h3>{{ __('You have to setup the Shop first.') }}</h3>
    @else
        <h3>{{ __('You have Shop. Very nice.') }} - {{ $scannedTotal }}</h3>
        <hr>
        @foreach ($items as $index => $item)
            @if ($item['itemId'] == '')
                <div class="input-group mb-3">
                    <input type="text" class="form-control" wire:model="items.{{ $index }}.name">
                    <input class="btn btn-outline-secondary" type="button" value="{{ __('Add new') }}" wire:click="insertNew({{ $index }})">
                </div>
            @endif
            <div class="input-group mb-3">
                <select class="form-select" id="selectedAddress-{{ $index }}" wire:model="items.{{ $index }}.itemId">
                    <option value="" @if($item['itemId'] == '') selected @endif>{{ __('Scanned text') }}</option>
                    @foreach ($item['suggestions'] as $itemSuggestion)
                        <option value="{{ $itemSuggestion['id'] }}"
                            @if($item['itemId'] == $itemSuggestion['id']) selected @endif
                            >{{ $itemSuggestion['name'] }} ({{ $itemSuggestion['percentage'] }}%)</option>
                    @endforeach
                </select>
                <input type="text" class="form-control" wire:model="items.{{ $index }}.price">
                <span class="input-group-text">{{ __('Ft') }}</span>
                <button class="btn btn-outline-secondary" type="button" wire:click="deleteItem({{ $index }})"><i class="bi bi-trash-fill"></i></button>
            </div>
            @error('items.{{ $index }}.price') <span class="error">{{ $message }}</span> @enderror
            <hr>
        @endforeach
    @endif
</div>
