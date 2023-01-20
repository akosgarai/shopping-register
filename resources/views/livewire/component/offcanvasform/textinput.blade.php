<div class="mb-3">
    <label for="{{ $modelId }}" class="form-label">{{ $formLabel }}</label>
    <input type="text" class="form-control" id="{{ $modelId }}" wire:model="{{ $modelId }}" @if(!empty($readonly)) readonly disabled @endif>
    <span id="errors-{{ $modelId }}" class="text-danger" style="display: none;"></span>
</div>
