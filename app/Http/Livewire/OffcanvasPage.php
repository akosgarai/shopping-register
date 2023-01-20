<?php

namespace App\Http\Livewire;

use Livewire\Component;

abstract class OffcanvasPage extends Component
{
    public const ACTION_UPDATE = 'update';

    // This parameter is used to determine which offcanvas to show.
    // If it is empty then no offcanvas is shown.
    public $action = '';

    // It sets the action parameter to the value passed in
    // then calls the initialize() method.
    public function setAction($action)
    {
        $this->action = $action;
        if ($action != self::ACTION_UPDATE) {
            $this->initialize();
        }
    }

    // Event handler for the offcanvas close event.
    // It sets the action parameter to empty.
    // It is necessary, because the offcanvas is closed by clicking on the overlay.
    // In this case the function wired to the close button is not called.
    public function offcanvasClose()
    {
        $this->setAction('');
    }

    // Initialize the component based on the query string parameters.
    public function mount()
    {
        $this->action = request()->query('action', '');
        $id = request()->query('id', '');
        if ($id != '') {
            $this->load($id);
        }
    }

    // This method is called when the action parameter is set to empty.
    abstract public function initialize();

    // This method is called when the new model needs to be saved.
    abstract public function saveNew();

    // This method is called when the existing model needs to be updated.
    abstract public function update();

    // This method is called when the existing model needs to be deleted.
    abstract public function delete($id);

    // This method is called when a model needs to be loaded.
    abstract public function load($id);
}
