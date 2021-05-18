<?php

namespace CupNoodles\ExtraVars\Requests;

use System\Classes\FormRequest;

class ExtraVars extends FormRequest
{
    public function rules()
    {
        return [
            ['name', 'cupnoodles.extravars::default.name', 'required|between:2,128'],
            ['class', 'cupnoodles.extravars::default.class', 'required'],
            ['type', 'cupnoodles.extravars::default.type', 'required'],

        ];
    }

}
