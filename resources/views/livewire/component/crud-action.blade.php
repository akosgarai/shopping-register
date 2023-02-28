<div>
    @if($action == self::ACTION_CREATE)
        <div class="card">
            <h4 class="card-header"><i class="bi bi-plus-circle me-3"></i>{{ __('Create') }}</h4>
            <div class="card-body">
                <form wire:submit.prevent="create">
                    @foreach($formData as $key => $formElement)
                        @if($formElement['type'] == 'textinput' && $formElement['readonly'] == false)
                            @include('livewire.component.offcanvasform.textinput', ['modelId' => 'modelData.' . $formElement['keyName'], 'formLabel' => $formElement['label']])
                        @endif
                        @if($formElement['type'] == 'selectoraddress' && $formElement['readonly'] == false)
                            @include('livewire.component.offcanvasform.selectoraddress', [
                                'modelId' => 'modelData.' . $formElement['keyName'],
                                'addresses' => $formElement['options'],
                                'selected' => $modelData[$formElement['keyName']]
                            ])
                        @endif
                        @if($formElement['type'] == 'selectorcompany' && $formElement['readonly'] == false)
                            @include('livewire.component.offcanvasform.selectorcompany', [
                                'modelId' => 'modelData.' . $formElement['keyName'],
                                'companies' => $formElement['options'],
                                'selected' => $modelData[$formElement['keyName']]
                            ])
                        @endif
                        @if($formElement['type'] == 'selectorshop' && $formElement['readonly'] == false)
                            @include('livewire.component.offcanvasform.selectorshop', [
                                'modelId' => 'modelData.' . $formElement['keyName'],
                                'shops' => $formElement['options'],
                                'selected' => $modelData[$formElement['keyName']]
                            ])
                        @endif
                        @if($formElement['type'] == 'datetimelocalinput' && $formElement['readonly'] == false)
                            @include('livewire.component.offcanvasform.datetimelocalinput', ['modelId' => 'modelData.' . $formElement['keyName'], 'formLabel' => $formElement['label']])
                        @endif
                    @endforeach
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
                @foreach($formData as $key => $formElement)
                    @if($formElement['type'] == 'textinput' && $formElement['readonly'] == false)
                        <p class="card-text fw-bold ms-2 fs-3">{{ $modelData[$formElement['keyName']] }}</p>
                    @endif
                @endforeach
            </div>
        </div>
    @elseif($action == self::ACTION_UPDATE)
        <div class="card">
            <h4 class="card-header"><i class="bi bi-pencil-square me-3"></i>{{ __('Update') }}</h4>
            <div class="card-body">
                <form wire:submit.prevent="update">
                    @foreach($formData as $key => $formElement)
                        @if($formElement['type'] == 'textinput')
                            @include('livewire.component.offcanvasform.textinput', ['modelId' => 'modelData.' . $formElement['keyName'], 'formLabel' => $formElement['label'], 'readonly' => $formElement['readonly']])
                        @endif
                        @if($formElement['type'] == 'selectoraddress' && $formElement['readonly'] == false)
                            @include('livewire.component.offcanvasform.selectoraddress', [
                                'modelId' => 'modelData.' . $formElement['keyName'],
                                'addresses' => $formElement['options'],
                                'selected' => $modelData[$formElement['keyName']]
                            ])
                        @endif
                        @if($formElement['type'] == 'selectorcompany' && $formElement['readonly'] == false)
                            @include('livewire.component.offcanvasform.selectorcompany', [
                                'modelId' => 'modelData.' . $formElement['keyName'],
                                'companies' => $formElement['options'],
                                'selected' => $modelData[$formElement['keyName']]
                            ])
                        @endif
                        @if($formElement['type'] == 'selectorshop' && $formElement['readonly'] == false)
                            @include('livewire.component.offcanvasform.selectorshop', [
                                'modelId' => 'modelData.' . $formElement['keyName'],
                                'shops' => $formElement['options'],
                                'selected' => $modelData[$formElement['keyName']]
                            ])
                        @endif
                        @if($formElement['type'] == 'datetimelocalinput' && $formElement['readonly'] == false)
                            @include('livewire.component.offcanvasform.datetimelocalinput', ['modelId' => 'modelData.' . $formElement['keyName'], 'formLabel' => $formElement['label']])
                        @endif
                    @endforeach
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
                @foreach($formData as $key => $formElement)
                    @if($formElement['type'] == 'textinput' && $formElement['readonly'] == false)
                        <p class="card-text fw-bold ms-2 fs-3">{{ $modelData[$formElement['keyName']] }}</p>
                    @endif
                @endforeach
                <div class="d-flex flex-row-reverse">
                    <button type="button" class="btn btn-danger" wire:click="delete">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
