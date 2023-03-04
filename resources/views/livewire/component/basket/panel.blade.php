<div>
    <livewire:component.crud-action
        :modelName="'basket'" :action="$contentParameters['action']"
        :formData="$contentParameters['formData']"
        :modelData="$contentParameters['basket']"
        :viewData="$contentParameters['viewData']"
        :wire:key="'company-panel'" />
</div>
