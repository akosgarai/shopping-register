<div>
    <livewire:component.crud-action
        :modelName="'address'" :action="$contentParameters['action']"
        :formData="[
            ['keyName' => 'raw', 'type' => 'textinput', 'label' => __('Address'), 'rules' => 'required|string|max:255', 'readonly' => false],
            ['keyName' => 'createdAt', 'type' => 'textinput', 'label' => __('Created'), 'rules' => '', 'readonly' => true],
            ['keyName' => 'updatedAt', 'type' => 'textinput', 'label' => __('Updated'), 'rules' => '', 'readonly' => true],
        ]"
        :modelData="$contentParameters['address']"
        :wire:key="'address-panel'" />
</div>
