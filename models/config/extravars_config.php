<?php


$config['list']['toolbar'] = [
    'buttons' => [
        'create' => [
            'label' => 'lang:admin::lang.button_new',
            'class' => 'btn btn-primary',
            'href' => admin_url('cupnoodles/extravars/extravars/create'),
        ],
        'delete' => [
            'label' => 'lang:admin::lang.button_delete',
            'class' => 'btn btn-danger',
            'data-attach-loading' => '',
            'data-request' => 'onDelete',
            'data-request-form' => '#list-form',
            'data-request-data' => "_method:'DELETE'",
            'data-request-confirm' => 'lang:admin::lang.alert_warning_confirm',
        ],
    ],
];

$config['list']['columns'] = [
    'edit' => [
        'type' => 'button',
        'iconCssClass' => 'fa fa-pencil',
        'attributes' => [
            'class' => 'btn btn-edit',
            'href' => admin_url('cupnoodles/extravars/extravars/edit/{extra_vars_id}'),
        ],
    ],
    'name' => [
        'label' => 'lang:cupnoodles.extravars::default.name',
        'type' => 'text'
    ],
    'type' => [
        'label' => 'lang:cupnoodles.extravars::default.type',
        'type' => 'text'
    ],
    'class'  => [
        'label' => 'lang:cupnoodles.extravars::default.class',
        'type' => 'text'
    ],
    'extra_vars_id' => [
        'label' => 'lang:admin::lang.column_id',
        'invisible' => TRUE,
    ],

];

$config['form']['toolbar'] = [
    'buttons' => [
        'save' => [
            'label' => 'lang:admin::lang.button_save',
            'class' => 'btn btn-primary',
            'data-request' => 'onSave',
            'data-progress-indicator' => 'admin::lang.text_saving',
        ],
        'saveClose' => [
            'label' => 'lang:admin::lang.button_save_close',
            'class' => 'btn btn-default',
            'data-request' => 'onSave',
            'data-request-data' => 'close:1',
            'data-progress-indicator' => 'admin::lang.text_saving',
        ],
        'delete' => [
            'label' => 'lang:admin::lang.button_icon_delete',
            'class' => 'btn btn-danger',
            'data-request' => 'onDelete',
            'data-request-data' => "_method:'DELETE'",
            'data-request-confirm' => 'lang:admin::lang.alert_warning_confirm',
            'data-progress-indicator' => 'admin::lang.text_deleting',
            'context' => ['edit'],
        ],
    ],
];

$config['form']['tabs'] = [
    'defaultTab' => 'lang:cupnoodles.extravars::default.text_tab_general',
    'fields' => [
        'name' => [
            'label' => 'lang:cupnoodles.extravars::default.name',
            'type' => 'text',
        ],
        'slug' => [
            'label' => 'lang:cupnoodles.extravars::default.slug',
            'type' => 'text'
        ],
        'class' => [
            'label' => 'lang:cupnoodles.extravars::default.class',
            'type' => 'radiotoggle',
            'span' => 'left',
            'options' => [
                'Menu' => 'lang:cupnoodles.extravars::default.menu',
                'Category' => 'lang:cupnoodles.extravars::default.category',
                'Location' => 'lang:cupnoodles.extravars::default.location',
            ],
        ],
        'type' => [
            'label' => 'lang:cupnoodles.extravars::default.type',
            'type' => 'radiotoggle',
            'options' => [
                'String' => 'lang:cupnoodles.extravars::default.string',
                'Int' => 'lang:cupnoodles.extravars::default.int',
                'Bool' => 'lang:cupnoodles.extravars::default.bool'
            ],
        ],
    ],
];

return $config;
