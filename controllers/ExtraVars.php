<?php

namespace CupNoodles\ExtraVars\Controllers;

class ExtraVars extends \Admin\Classes\AdminController
{
    public $implement = [
        'Admin\Actions\ListController',
        'Admin\Actions\FormController'
    ];

    public $listConfig = [
        'list' => [
            'model' => 'CupNoodles\ExtraVars\Models\ExtraVars',
            'title' => 'cupnoodles.extravars::default.text_title',
            'emptyMessage' => 'cupnoodles.extravars::default.text_empty',
            'defaultSort' => ['extra_vars_id_id', 'DESC'],
            'configFile' => 'extravars_config',
        ],
    ];

    public $formConfig = [
        'name' => 'cupnoodles.extravars::default.text_form_name',
        'model' => 'CupNoodles\ExtraVars\Models\ExtraVars',
        'request' => 'CupNoodles\ExtraVars\Requests\ExtraVars',
        'create' => [
            'title' => 'lang:admin::lang.form.create_title',
            'redirect' => 'cupnoodles/extravars/extravars/edit/{extra_vars_id}',
            'redirectClose' => 'cupnoodles/extravars/extravars',
        ],
        'edit' => [
            'title' => 'lang:admin::lang.form.edit_title',
            'redirect' => 'cupnoodles/extravars/extravars/edit/{extra_vars_id}',
            'redirectClose' => 'cupnoodles/extravars/extravars',
        ],
        'preview' => [
            'title' => 'lang:admin::lang.form.preview_title',
            'redirect' => 'cupnoodles/extravars/extravars',
        ],
        'delete' => [
            'redirect' => 'cupnoodles/extravars/extravars',
        ],
        'configFile' => 'extravars_config',
    ];

}
