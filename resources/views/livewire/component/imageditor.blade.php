<div class="input-group mb-3">
@if($editMode)
    <button class="btn btn-outline-secondary" type="button" wire:click="broadcastApply">Apply</button>
    <span class="input-group-text">Options</span>
    <button class="btn btn-outline-secondary" type="button" wire:click="broadcastCancel">Cancel</button>
@else
    <button class="btn btn-outline-secondary" type="button" wire:click="broadcastCrop">Crop</button>
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{ __('Filter') }}</button>
    <ul class="dropdown-menu">
        @foreach($filters as $name => $active)
            <li style="padding-left: 5px;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="filter{{ $name }}" wire:click="broadcastFilter('{{ $name }}')">
                    <label class="form-check-label" for="filter{{ $name }}">{{ $name }}</label>
                </div>
            </li>
        @endforeach
    </ul>
    <button class="btn btn-outline-secondary" type="button" wire:click="broadcastEditComplete">Next</button>
@endif
</div>
