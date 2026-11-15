<?php

declare(strict_types=1);

namespace CupNoodles\ExtraVars\Models;

use CupNoodles\ExtraVars\Classes\ExtraVarManager;
use Igniter\Flame\Database\Model;
use Illuminate\Support\Str;

/**
 * Definition of an extra field shown on one kind of record (menu item, category or location).
 *
 * @property int $extra_var_id
 * @property string $name
 * @property string $code Used in templates: $menuItem->getExtraVarValue('code')
 * @property string $type One of self::TYPES
 * @property string $model Morph alias of the record type, eg. "menus"
 */
class ExtraVar extends Model
{
    /** Field type => admin form widget */
    public const array TYPES = [
        'text' => 'text',
        'number' => 'number',
        'switch' => 'switch',
    ];

    protected $table = 'extra_vars';

    protected $primaryKey = 'extra_var_id';

    public $timestamps = true;

    protected $fillable = ['name', 'code', 'type', 'model'];

    public $relation = [
        'hasMany' => [
            'values' => [ExtraVarValue::class, 'foreignKey' => 'extra_var_id', 'delete' => true],
        ],
    ];

    protected function beforeSave()
    {
        // Only generate a code when none is given: changing an existing one would break templates.
        if (!$this->code) {
            $this->code = Str::slug((string)$this->name, '_');
        }
    }

    protected function afterSave()
    {
        ExtraVarManager::clearDefinitionCache();
    }

    protected function afterDelete()
    {
        ExtraVarManager::clearDefinitionCache();
    }

    public static function getTypeOptions(): array
    {
        return collect(array_keys(self::TYPES))
            ->mapWithKeys(fn(string $type): array => [$type => 'lang:cupnoodles.extravars::default.type_'.$type])
            ->all();
    }

    public static function getModelOptions(): array
    {
        return collect(ExtraVarManager::MODELS)
            ->mapWithKeys(fn(array $target, string $alias): array => [$alias => $target['label']])
            ->all();
    }

    public function getModelLabelAttribute(): string
    {
        return lang(ExtraVarManager::MODELS[$this->model]['label'] ?? $this->model);
    }

    public function getTypeLabelAttribute(): string
    {
        return lang('cupnoodles.extravars::default.type_'.$this->type);
    }

    /**
     * Converts a stored string into the PHP type templates expect.
     */
    public function castValue(?string $value): mixed
    {
        if ($value === null || $value === '') {
            return $this->type === 'switch' ? false : null;
        }

        return match ($this->type) {
            'switch' => (bool)$value,
            'number' => is_numeric($value) ? $value + 0 : null,
            default => $value,
        };
    }
}
