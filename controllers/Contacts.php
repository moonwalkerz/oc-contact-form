<?php

namespace MoonWalkerz\Contact\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Contacts extends Controller
{
    public $implement = [
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ReorderController::class,
    ];

    public $requiredPermissions = ['moonwalkerz.contact.access_contacts'];
    public $listConfig = 'config_list.yaml';

    public $formConfig = 'config_form.yaml';

    public $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('MoonWalkerz.Contact', 'contacts', 'side-contacts');
    }
}
