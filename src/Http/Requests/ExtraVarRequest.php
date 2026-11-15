<?php

declare(strict_types=1);

namespace CupNoodles\ExtraVars\Http\Requests;

use CupNoodles\ExtraVars\Classes\ExtraVarManager;
use CupNoodles\ExtraVars\Models\ExtraVar;
use Igniter\System\Classes\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ExtraVarRequest extends FormRequest
{
    public function attributes(): array
    {
        return [
            'name' => lang('cupnoodles.extravars::default.label_name'),
            'code' => lang('cupnoodles.extravars::default.label_code'),
            'model' => lang('cupnoodles.extravars::default.label_model'),
            'type' => lang('cupnoodles.extravars::default.label_type'),
        ];
    }

    protected function prepareForValidation(): void
    {
        // Same generated code as the model, so uniqueness is checked on the saved value
        if (!$this->input('code')) {
            $this->merge(['code' => Str::slug((string)$this->input('name'), '_')]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'between:2,255'],
            'model' => ['required', Rule::in(array_keys(ExtraVarManager::MODELS))],
            'code' => ['required', 'alpha_dash', 'max:255',
                Rule::unique('extra_vars', 'code')
                    ->where('model', $this->input('model'))
                    ->ignore($this->getRecordId(), 'extra_var_id'),
            ],
            'type' => ['required', Rule::in(array_keys(ExtraVar::TYPES))],
        ];
    }
}
