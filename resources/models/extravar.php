<?php

use CupNoodles\ExtraVars\Models\ExtraVar;

$config['list']['filter'] = [
    'search' => [
        'prompt' => 'lang:cupnoodles.extravars::default.text_filter_search',
        'mode' => 'all',
    ],
    'scopes' => [
        'model' => [
            'label' => 'lang:cupnoodles.extravars::default.text_filter_model',
            'type' => 'select',
            'conditions' => 'model = :filtered',
            'modelClass' => ExtraVar::class,
            'options' => 'getModelOptions',
        ],
    ],
];

$config['list']['toolbar'] = [
    'buttons' => [
        'create' => [
            'label' => 'lang:igniter::admin.button_new',
            'class' => 'btn btn-primary',
            'href' => 'cupnoodles/extravars/extra_vars/create',
        ],
    ],
];

$config['list']['bulkActions'] = [
    'delete' => [
        'label' => 'lang:igniter::admin.button_delete',
        'class' => 'btn btn-light text-danger',
        'data-request-confirm' => 'lang:cupnoodles.extravars::default.alert_delete_confirm',
    ],
];

$config['list']['columns'] = [
    'edit' => [
        'type' => 'button',
        'iconCssClass' => 'fa fa-pencil',
        'attributes' => [
            'class' => 'btn btn-edit',
            'href' => 'cupnoodles/extravars/extra_vars/edit/{extra_var_id}',
        ],
    ],
    'name' => [
        'label' => 'lang:cupnoodles.extravars::default.label_name',
        'type' => 'text',
        'searchable' => true,
    ],
    'code' => [
        'label' => 'lang:cupnoodles.extravars::default.label_code',
        'type' => 'text',
        'searchable' => true,
    ],
    'model_label' => [
        'label' => 'lang:cupnoodles.extravars::default.label_model',
        'type' => 'text',
        'sortable' => false,
    ],
    'type_label' => [
        'label' => 'lang:cupnoodles.extravars::default.label_type',
        'type' => 'text',
        'sortable' => false,
    ],
    'extra_var_id' => [
        'label' => 'lang:igniter::admin.column_id',
        'invisible' => true,
    ],
];

$config['form']['toolbar'] = [
    'buttons' => [
        'save' => [
            'label' => 'lang:igniter::admin.button_save',
            'context' => ['create', 'edit'],
            'partial' => 'form/toolbar_save_button',
            'class' => 'btn btn-primary',
            'data-request' => 'onSave',
            'data-progress-indicator' => 'igniter::admin.text_saving',
        ],
        'delete' => [
            'label' => 'lang:igniter::admin.button_icon_delete',
            'context' => ['edit'],
            'class' => 'btn btn-danger',
            'data-request' => 'onDelete',
            'data-request-submit' => 'true',
            'data-request-confirm' => 'lang:cupnoodles.extravars::default.alert_delete_confirm',
        ],
    ],
];

$config['form']['fields'] = [
    'name' => [
        'label' => 'lang:cupnoodles.extravars::default.label_name',
        'type' => 'text',
        'span' => 'left',
        'comment' => 'lang:cupnoodles.extravars::default.help_name',
    ],
    'code' => [
        'label' => 'lang:cupnoodles.extravars::default.label_code',
        'type' => 'text',
        'span' => 'right',
        'comment' => 'lang:cupnoodles.extravars::default.help_code',
    ],
    'model' => [
        'label' => 'lang:cupnoodles.extravars::default.label_model',
        'type' => 'radiotoggle',
        'span' => 'left',
        'default' => 'menus',
        'options' => [ExtraVar::class, 'getModelOptions'],
    ],
    'type' => [
        'label' => 'lang:cupnoodles.extravars::default.label_type',
        'type' => 'radiotoggle',
        'span' => 'right',
        'default' => 'text',
        'options' => [ExtraVar::class, 'getTypeOptions'],
    ],
];

return $config;
