<div>
    <livewire:component.crud-action
        :modelName="'company'" :action="$contentParameters['action']"
        :formData="[
            ['keyName' => 'name', 'type' => 'textinput', 'label' => __('Company Name'), 'rules' => 'required|string|max:255', 'readonly' => false],
            ['keyName' => 'taxNumber', 'type' => 'textinput', 'label' => __('Tax Number'), 'rules' => ['create' =>'required|string|unique:companies,tax_number|digits:11', 'update' => 'required|string|digits:11'], 'readonly' => false],
            ['keyName' => 'address', 'type' => 'selectoraddress', 'rules' => 'required|integer|exists:addresses,id', 'readonly' => false, 'options' => $contentParameters['addresses']],
            ['keyName' => 'createdAt', 'type' => 'textinput', 'label' => __('Created'), 'rules' => '', 'readonly' => true],
            ['keyName' => 'updatedAt', 'type' => 'textinput', 'label' => __('Updated'), 'rules' => '', 'readonly' => true],
        ]"
        :modelData="$contentParameters['company']"
        :viewData="$contentParameters['viewData']"
        :wire:key="'company-panel'" />
</div>
