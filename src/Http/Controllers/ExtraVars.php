<?php

declare(strict_types=1);

namespace CupNoodles\ExtraVars\Http\Controllers;

use CupNoodles\ExtraVars\Http\Requests\ExtraVarRequest;
use CupNoodles\ExtraVars\Models\ExtraVar;
use Igniter\Admin\Classes\AdminController;
use Igniter\Admin\Facades\AdminMenu;
use Igniter\Admin\Http\Actions\FormController;
use Igniter\Admin\Http\Actions\ListController;

class ExtraVars extends AdminController
{
    public array $implement = [
        ListController::class,
        FormController::class,
    ];

    public array $listConfig = [
        'list' => [
            'model' => ExtraVar::class,
            'title' => 'lang:cupnoodles.extravars::default.text_title',
            'emptyMessage' => 'lang:cupnoodles.extravars::default.text_empty',
            'defaultSort' => ['extra_var_id', 'DESC'],
            'configFile' => 'extravar',
        ],
    ];

    public array $formConfig = [
        'name' => 'lang:cupnoodles.extravars::default.text_form_name',
        'model' => ExtraVar::class,
        'request' => ExtraVarRequest::class,
        'create' => [
            'title' => 'lang:igniter::admin.form.create_title',
            'redirect' => 'cupnoodles/extravars/extra_vars/edit/{extra_var_id}',
            'redirectClose' => 'cupnoodles/extravars/extra_vars',
            'redirectNew' => 'cupnoodles/extravars/extra_vars/create',
        ],
        'edit' => [
            'title' => 'lang:igniter::admin.form.edit_title',
            'redirect' => 'cupnoodles/extravars/extra_vars/edit/{extra_var_id}',
            'redirectClose' => 'cupnoodles/extravars/extra_vars',
            'redirectNew' => 'cupnoodles/extravars/extra_vars/create',
        ],
        'preview' => [
            'title' => 'lang:igniter::admin.form.preview_title',
            'back' => 'cupnoodles/extravars/extra_vars',
        ],
        'delete' => [
            'redirect' => 'cupnoodles/extravars/extra_vars',
        ],
        'configFile' => 'extravar',
    ];

    protected null|string|array $requiredPermissions = 'CupNoodles.ExtraVars.Manage';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('extravars', 'design');
    }
}
