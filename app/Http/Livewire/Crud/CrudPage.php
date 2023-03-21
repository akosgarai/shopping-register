<?php

namespace App\Http\Livewire\Crud;

abstract class CrudPage extends Crud
{
    // The name of the rendered view.
    public $templateName = '';

    // The id of the entity to be updated.
    public $modelId = '';

    // The data for the view template.
    public $viewData = null;

    // the timestamps of the entity.
    public $createdAt = '';
    public $updatedAt = '';

    // The query string parameters.
    protected $queryString = [
        'action' => ['except' => ''],
        'modelId' => ['except' => '', 'as' => 'id'],
    ];

    // Event handler for the panel close event.
    public function clearAction()
    {
        $this->action = '';
        $this->initialize();
    }

    // Initialize the component based on the query string parameters.
    public function mount()
    {
        $modelId = request()->query('id', '');
        if ($modelId != '') {
            $this->load($modelId);
        }
        $this->action = request()->query('action', '');
    }

    // Render function. It extends the app layout and renders the template.
    public function render()
    {
        return view($this->templateName, $this->getTemplateParameters())
            ->extends('layouts.app');
    }

    // This method is called to setup the data for the current action.
    abstract public function initialize();

    // This method is called when the new model needs to be saved.
    abstract public function saveNew(array $model);

    // This method is called when the existing model needs to be updated.
    abstract public function update(array $model);

    // This method is called when the existing model needs to be deleted.
    abstract public function delete($modelId);

    // This method is called when a model needs to be loaded.
    public function load($modelId)
    {
        $this->modelId = $modelId;
        $this->action = self::ACTION_UPDATE;
        $this->initialize();
    }

    // This method is called when a model needs to be loaded for deletion.
    public function loadForDelete($modelId)
    {
        $this->modelId = $modelId;
        $this->action = self::ACTION_DELETE;
        $this->initialize();
    }

    // This method is called when a model needs to be loaded for deletion.
    public function loadForView($modelId)
    {
        $this->modelId = $modelId;
        $this->action = self::ACTION_READ;
        $this->initialize();
    }

    // This method has to return the parameters that needs to be added to the
    // rendered template.
    abstract public function getTemplateParameters();
}
