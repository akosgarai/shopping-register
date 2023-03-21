<?php

namespace App\Http\Livewire;

use Livewire\Component;

abstract class Crud extends Component
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

    // This parameter is used to determine which form to show.
    public $action = self::ACTION_READ;

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

    // This method is called to setup the data for the current action.
    abstract public function initialize();
}
