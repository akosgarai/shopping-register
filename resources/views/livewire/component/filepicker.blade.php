<div class="input-group mb-3">
    <input type="file" class="form-control" wire:model="file">
    <button class="btn btn-outline-secondary" type="button" @if(!$file) disabled @endif>{{ __('Upload') }}</button>
</div>
