<?php

namespace MoonWalkerz\Contact\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Agenda extends Controller
{
    public $implement = [
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ImportExportController::class,
    ];

    public $requiredPermissions = ['moonwalkerz.contact.access_agenda'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('MoonWalkerz.Contact', 'contacts', 'side-agenda');
    }
}
