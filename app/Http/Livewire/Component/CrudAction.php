<?php

namespace App\Http\Livewire\Component;

use App\Http\Livewire\Crud;

class CrudAction extends Crud
{
    /*
     * Form element:
     * - type: text (the name of the input template) Possible values: textinput
     * - keyName: text (name of the property)
     * - label: text (label of the input)
     * - rules: text (validation rules)
     * */
    public $formData = [];
    // The model name that is used to determine the model class.
    // The crud event name is based on this value.
    public $modelName = '';
    // The model data. The key is the model attribute name.
    public $modelData = [];

    protected $listeners = [
        'crudaction.update' => 'updateData',
    ];

    public function updateData($data)
    {
        if (array_key_exists('formData', $data)) {
            $this->formData = $data['formData'];
        }
        if (!array_key_exists($this->modelName, $data)) {
            return;
        }
        $this->modelData = $data[$this->modelName];
        $this->setAction($data['action']);
    }

    public function initialize()
    {
        // empty the errors.
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.component.crud-action');
    }

    // LiveWire updated hook handler
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function create()
    {
        $this->validate();
        $this->emit($this->modelName . '.create', $this->modelData);
    }

    public function update()
    {
        $this->validate();
        $this->emit($this->modelName . '.update', $this->modelData);
    }
    public function delete()
    {
        $this->emit($this->modelName . '.delete', $this->modelData['id']);
    }

    protected function rules()
    {
        $rules = [];
        foreach ($this->formData as $formElement) {
            $rules['modelData.'.$formElement['keyName']] = isset($formElement['rules'][$this->action]) ? $formElement['rules'][$this->action] : $formElement['rules'];
        }
        return $rules;
    }
}
