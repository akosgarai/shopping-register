<div>
    @if($action == self::ACTION_CREATE)
        <div class="card">
            <h4 class="card-header"><i class="bi bi-plus-circle me-3"></i>{{ __('Create') }}</h4>
            <div class="card-body">
                <form wire:submit.prevent="create">
                    @include('livewire.component.forms.formitems', ['formData' => $formData])
                    <div class="d-flex flex-row-reverse">
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @elseif($action == self::ACTION_READ)
        <div class="card">
            <h4 class="card-header"><i class="bi bi-eyeglasses me-3"></i>{{ __('View') }}</h4>
            <div class="card-body">
                @include('livewire.component.' . $modelName . '.view', ['model' => $viewData, 'edit' => false])
            </div>
        </div>
    @elseif($action == self::ACTION_UPDATE)
        <div class="card">
            <h4 class="card-header"><i class="bi bi-pencil-square me-3"></i>{{ __('Update') }}</h4>
            <div class="card-body">
                <form wire:submit.prevent="update">
                    @include('livewire.component.forms.formitems', ['formData' => $formData])
                    <div class="d-flex flex-row-reverse">
                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @elseif($action == self::ACTION_DELETE)
        <div class="card">
            <h4 class="card-header"><i class="bi bi-trash me-3"></i>{{ __('Delete') }}</h4>
            <div class="card-body">
                <h5 class="card-title mb-2">{{ __('Are you sure you want to delete?') }}</h5>
                @include('livewire.component.' . $modelName . '.view', ['model' => $viewData, 'edit' => false])
                <div class="d-flex flex-row-reverse">
                    <button type="button" class="btn btn-danger" wire:click="delete">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
