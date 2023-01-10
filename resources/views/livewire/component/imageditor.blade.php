<div class="input-group mb-3">
@if($editMode)
    <button class="btn btn-outline-secondary" type="button" wire:click="broadcastApply">Apply</button>
    <button class="btn btn-outline-secondary" type="button" wire:click="broadcastCancel">Cancel</button>
@else
    <button class="btn btn-outline-secondary" type="button" wire:click="broadcastCrop">Crop</button>
    <button class="btn btn-outline-secondary" type="button" wire:click="broadcastEditComplete">Next</button>
@endif
</div>
