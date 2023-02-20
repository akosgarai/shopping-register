<?php

namespace App\Http\Livewire;

use Livewire\Component;

abstract class CrudPage extends Component
{
    public const ACTION_CREATE = 'create';
    public const ACTION_READ = 'view';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';

    public const ACTIONS = [
        self::ACTION_CREATE,
        self::ACTION_READ,
        self::ACTION_UPDATE,
        self::ACTION_DELETE,
    ];

    // This parameter is used to determine which panel to show.
    // If it is empty then no panel is shown.
    public $action = '';

    // The name of the rendered view.
    public $templateName = '';

    // The id of the entity to be updated.
    public $modelId = '';

    // the timestamps of the entity.
    public $createdAt = '';
    public $updatedAt = '';

    // The query string parameters.
    protected $queryString = [
        'action' => ['except' => ''],
        'modelId' => ['except' => '', 'as' => 'id'],
    ];

    // It sets the action parameter to the value passed in
    // then calls the initialize() method.
    public function setAction($action)
    {
        if (!in_array($action, self::ACTIONS)) {
            return;
        }
        $this->action = $action;
        $this->initialize();
    }

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
