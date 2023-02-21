<div>
    <livewire:component.crud-action
        :modelName="'address'" :action="$contentParameters['action']"
        :formData="[
            ['keyName' => 'raw', 'type' => 'textinput', 'label' => __('Address'), 'rules' => 'required|string|max:255'],
        ]"
        :modelData="$contentParameters['address']"
        :wire:key="'address-panel'" />
</div>
