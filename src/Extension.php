<?php

declare(strict_types=1);

namespace CupNoodles\ExtraVars;

use CupNoodles\ExtraVars\Classes\ExtraVarManager;
use CupNoodles\ExtraVars\Models\ExtraVar;
use CupNoodles\ExtraVars\Models\ExtraVarValue;
use Igniter\System\Classes\BaseExtension;
use Override;

class Extension extends BaseExtension
{
    protected array $morphMap = [
        'extra_vars' => ExtraVar::class,
        'extra_var_values' => ExtraVarValue::class,
    ];

    #[Override]
    public function boot(): void
    {
        ExtraVarManager::boot();
    }

    #[Override]
    public function registerPermissions(): array
    {
        return [
            'CupNoodles.ExtraVars.Manage' => [
                'description' => 'lang:cupnoodles.extravars::default.permission_manage',
                'group' => 'igniter::admin.permissions.name',
            ],
        ];
    }

    #[Override]
    public function registerNavigation(): array
    {
        return [
            'design' => [
                'child' => [
                    'extravars' => [
                        'priority' => 90,
                        'class' => 'extravars',
                        'href' => admin_url('cupnoodles/extravars/extra_vars'),
                        'title' => lang('cupnoodles.extravars::default.side_menu'),
                        'permission' => 'CupNoodles.ExtraVars.Manage',
                    ],
                ],
            ],
        ];
    }
}
