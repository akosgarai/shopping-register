<div>
    <livewire:component.crud-action
        :modelName="'shop'" :action="$contentParameters['action']"
        :formData="[
            ['keyName' => 'name', 'type' => 'textinput', 'label' => __('Shop Name'), 'rules' => 'required|string|max:255', 'readonly' => false],
            ['keyName' => 'company', 'type' => 'selectorcompany', 'rules' => 'required|integer|exists:companies,id', 'readonly' => false, 'options' => $contentParameters['companies']],
            ['keyName' => 'address', 'type' => 'selectoraddress', 'rules' => 'required|integer|exists:addresses,id', 'readonly' => false, 'options' => $contentParameters['addresses']],
            ['keyName' => 'createdAt', 'type' => 'textinput', 'label' => __('Created'), 'rules' => '', 'readonly' => true],
            ['keyName' => 'updatedAt', 'type' => 'textinput', 'label' => __('Updated'), 'rules' => '', 'readonly' => true],
        ]"
        :modelData="$contentParameters['shop']"
        :viewData="$contentParameters['viewData']"
        :wire:key="'shop-panel'" />
</div>
