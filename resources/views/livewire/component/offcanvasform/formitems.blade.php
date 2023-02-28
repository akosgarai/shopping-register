@foreach($formData as $key => $formElement)
    @if($formElement['type'] == 'textinput' && ($action != self::ACTION_CREATE || $formElement['readonly'] == false))
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
