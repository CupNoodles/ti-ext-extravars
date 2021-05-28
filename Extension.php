<?php 

namespace CupNoodles\ExtraVars;

use System\Classes\BaseExtension;
use System\Classes\ExtensionManager;

use Event;
use Admin\Models\Menus_model;
use Admin\Models\Locations_model;
use Admin\Models\Categories_model;

use Admin\Widgets\Form;
use Admin\Classes\AdminController;


use CupNoodles\ExtraVars\Models\ExtraVars;
use CupNoodles\ExtraVars\Models\ExtraVarValues;

class Extension extends BaseExtension
{
    /**
     * Returns information about this extension.
     *
     * @return array
     */
    public function extensionMeta()
    {
        return [
            'name'        => 'Extra Vars',
            'author'      => 'CupNoodles',
            'description' => 'Add Extra Vars to Menu or Location',
            'icon'        => 'far fa-plus-square',
            'version'     => '1.0.0'
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {

        Menus_Model::saved(function ($model) {
            // save to the extra vars table instead of menus model
            foreach(ExtraVars::where('class', 'Menu')->get() as $ix=>$extra_var){
                $post_data = post();
                ExtraVarValues::updateOrCreate(
                    [
                        'extra_vars_id' => $extra_var->extra_vars_id,
                        'object_id' => $model->menu_id
                    ],
                    [
                        'value' => $post_data['Menu']['_'.$extra_var->slug]
                    ]
                    );
            }
        });

        Menus_Model::extend(function ($model) {
            $model->addDynamicMethod('getExtraVarValue', function($slug) use ($model) {
                $evv = ExtraVarValues::join('extra_vars', 'extra_vars.extra_vars_id', '=', 'extra_var_values.extra_vars_id')
                ->where([
                    ['extra_var_values.object_id', $model->menu_id],
                    ['extra_vars.slug', $slug]
                ])->first();
                if(isset($evv->value)){
                    return $evv->value;
                }
                return '';
                
            });
        });

        Categories_Model::saved(function ($model) {
            // save to the extra vars table instead of menus model
            foreach(ExtraVars::where('class', 'Category')->get() as $ix=>$extra_var){
                $post_data = post();
                ExtraVarValues::updateOrCreate(
                    [
                        'extra_vars_id' => $extra_var->extra_vars_id,
                        'object_id' => $model->category_id
                    ],
                    [
                        'value' => $post_data['Category']['_'.$extra_var->slug]
                    ]
                    );
            }
        });

        Categories_Model::extend(function ($model) {
            $model->addDynamicMethod('getExtraVarValue', function($slug) use ($model) {
                $evv = ExtraVarValues::join('extra_vars', 'extra_vars.extra_vars_id', '=', 'extra_var_values.extra_vars_id')
                ->where([
                    ['extra_var_values.object_id', $model->category_id],
                    ['extra_vars.slug', $slug]
                ])->first();
                if(isset($evv->value)){
                    return $evv->value;
                }
                return '';
                
            });
        });

        Locations_Model::saved(function ($model) {
            // save to the extra vars table instead of menus model
            foreach(ExtraVars::where('class', 'Location')->get() as $ix=>$extra_var){
                $post_data = post();
                ExtraVarValues::updateOrCreate(
                    [
                        'extra_vars_id' => $extra_var->extra_vars_id,
                        'object_id' => $model->location_id
                    ],
                    [
                        'value' => $post_data['Location']['_'.$extra_var->slug]
                    ]
                    );
            }
        });

        Locations_Model::extend(function ($model) {
            $model->addDynamicMethod('getExtraVarValue', function($slug) use ($model) {
                $evv = ExtraVarValues::join('extra_vars', 'extra_vars.extra_vars_id', '=', 'extra_var_values.extra_vars_id')
                ->where([
                    ['extra_var_values.object_id', $model->location_id],
                    ['extra_vars.slug', $slug]
                ])->first();
                if(isset($evv->value)){
                    return $evv->value;
                }
                return '';
                
            });
        });


        Event::listen('admin.form.extendFieldsBefore', function (Form $form) {

            if($form->model instanceof Menus_model){
                
                foreach(ExtraVars::where('class', 'Menu')->get() as $ix=>$extra_var){
                    switch ($extra_var->type){
                        case 'String': $field_type = 'text'; break;
                        case 'Int': $field_type = 'number'; break;
                        case 'Bool': $field_type = 'switch'; break;
                    }
                    $evv = ExtraVarValues::where([
                        ['object_id', $form->model->menu_id],
                        ['extra_vars_id', $extra_var->extra_vars_id]
                    ])->first();
                    
                    $form->tabs['fields']['_' . $extra_var->slug] = [
                        'label' => $extra_var->name,
                        'type' => $field_type,
                        'default' => isset($evv->value) ? $evv->value : ''
                    ];
                }
            }

            if($form->model instanceof Categories_model){

                foreach(ExtraVars::where('class', 'Category')->get() as $ix=>$extra_var){
                    switch ($extra_var->type){
                        case 'String': $field_type = 'text'; break;
                        case 'Int': $field_type = 'number'; break;
                        case 'Bool': $field_type = 'switch'; break;
                    }
                    $evv = ExtraVarValues::where([
                        ['object_id', $form->model->category_id],
                        ['extra_vars_id', $extra_var->extra_vars_id]
                    ])->first();

                    $form->tabs['fields']['_' . $extra_var->slug] = [
                        'label' => $extra_var->name,
                        'type' => $field_type,
                        'default' => isset($evv->value) ? $evv->value : ''
                    ];
                }

            }

            if($form->model instanceof Locations_model){
                
                foreach(ExtraVars::where('class', 'Location')->get() as $ix=>$extra_var){
                    switch ($extra_var->type){
                        case 'String': $field_type = 'text'; break;
                        case 'Int': $field_type = 'number'; break;
                        case 'Bool': $field_type = 'switch'; break;
                    }
                    $evv = ExtraVarValues::where([
                        ['object_id', $form->model->location_id],
                        ['extra_vars_id', $extra_var->extra_vars_id]
                    ])->first();

                    $form->tabs['fields']['_' . $extra_var->slug] = [
                        'label' => $extra_var->name,
                        'type' => $field_type,
                        'default' => isset($evv->value) ? $evv->value : ''
                    ];
                }
            }

        });




    }

    /**
     * Registers any admin permissions used by this extension.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'Admin.ExtraVars' => [
                'label' => 'cupnoodles.extravars::default.permissions',
                'group' => 'admin::lang.permissions.name',
            ],
        ];
    }


    public function registerNavigation()
    {
        return [
            'design' => [
                'child' => [
                    'extravars' => [
                        'priority' => 90,
                        'class' => 'ExtraVars',
                        'href' => admin_url('cupnoodles/extravars/extravars'),
                        'title' => lang('cupnoodles.extravars::default.side_menu'),
                        'permission' => 'Admin.ExtraVars',
                    ],
                ],
            ],
        ];
    }
}
