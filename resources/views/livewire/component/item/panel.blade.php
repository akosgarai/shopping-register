<div>
    <livewire:component.crud-action
        :modelName="'item'" :action="$contentParameters['action']"
        :formData="[
            ['keyName' => 'name', 'type' => 'textinput', 'label' => __('Item Name'), 'rules' => 'required|string|max:255', 'readonly' => false],
            ['keyName' => 'createdAt', 'type' => 'textinput', 'label' => __('Created'), 'rules' => '', 'readonly' => true],
            ['keyName' => 'updatedAt', 'type' => 'textinput', 'label' => __('Updated'), 'rules' => '', 'readonly' => true],
        ]"
        :modelData="$contentParameters['item']"
        :wire:key="'item-panel'" />
</div>
