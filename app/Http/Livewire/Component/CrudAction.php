<?php

namespace App\Http\Livewire\Component;

use Livewire\WithFileUploads;

use App\Http\Livewire\Crud\Crud;
use App\Services\ImageService;

class CrudAction extends Crud
{
    use WithFileUploads;
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
    // The model for the view.
    public $viewData = null;

    protected $listeners = [
        'crudaction.update' => 'updateData',
    ];

    public function updateData($data)
    {
        if (array_key_exists('formData', $data)) {
            $this->formData = $data['formData'];
        }
        if (array_key_exists('viewData', $data)) {
            $this->viewData = $data['viewData'];
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

    public function create(ImageService $imageService)
    {
        $this->validate();
        $this->storeImagesIfAny($imageService);
        $this->emit($this->modelName . '.create', $this->modelData);
    }

    public function update(ImageService $imageService)
    {
        $this->validate();
        $this->storeImagesIfAny($imageService);
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
            // The keyName is the model attribute name in most of the cases. The exception is the basket item.
            if (array_key_exists('keyName', $formElement)) {
                try {
                    $rules['modelData.'.$formElement['keyName']] = isset($formElement['rules'][$this->action]) ? $formElement['rules'][$this->action] : $formElement['rules'];
                } catch (\Exception $e) {
                    dd($formElement, $e->getMessage());
                }
            }
            if (array_key_exists('keyNameItem', $formElement) && array_key_exists('keyNamePrice', $formElement)) {
                $rules['modelData.'.$formElement['keyNameItem']] = $formElement['rulesItem'];
                $rules['modelData.'.$formElement['keyNamePrice']] = $formElement['rulesPrice'];
            }
        }
        return $rules;
    }

    private function storeImagesIfAny(ImageService $imageService)
    {
        // On case an image has been uploaded, Store it and set the modelData attribute.
        // select the images from the form data. The type is imageinput.
        $images = array_filter($this->formData, function ($element) {
            return $element['type'] == 'imageinput';
        });
        foreach ($images as $image) {
            if ($this->modelData[$image['keyName']] != null) {
                $this->modelData[$image['target']] = basename($imageService->saveReceiptImageToUserFolder($this->modelData[$image['keyName']], auth()->user()->id));
            }
        }
    }
}
