<?php

declare(strict_types=1);

namespace CupNoodles\ExtraVars\Classes;

use CupNoodles\ExtraVars\Models\ExtraVar;
use CupNoodles\ExtraVars\Models\ExtraVarValue;
use Igniter\Admin\Classes\FormTabs;
use Igniter\Admin\Widgets\Form;
use Igniter\Cart\Http\Controllers\Categories;
use Igniter\Cart\Http\Controllers\Menus;
use Igniter\Cart\Http\Requests\CategoryRequest;
use Igniter\Cart\Http\Requests\MenuRequest;
use Igniter\Cart\Models\Category;
use Igniter\Cart\Models\Menu;
use Igniter\Flame\Database\Model;
use Igniter\Local\Http\Controllers\Locations;
use Igniter\Local\Http\Requests\LocationRequest;
use Igniter\Local\Models\Location;
use Igniter\System\Classes\FormRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use WeakMap;

/**
 * Adds extra fields to menu items, categories and locations:
 * - an `extra_var_values` relation, eager loaded when any fields are defined
 * - `getExtraVarValue($code)` and `getExtraVars()` for templates
 * - the fields on each admin edit form, validated and saved through the normal Save button
 */
class ExtraVarManager
{
    /** Morph alias => model, admin controller, form request and label. Aliases match TastyIgniter's morph map. */
    public const array MODELS = [
        'menus' => [
            'model' => Menu::class,
            'controller' => Menus::class,
            'request' => MenuRequest::class,
            'label' => 'lang:cupnoodles.extravars::default.model_menus',
        ],
        'categories' => [
            'model' => Category::class,
            'controller' => Categories::class,
            'request' => CategoryRequest::class,
            'label' => 'lang:cupnoodles.extravars::default.model_categories',
        ],
        'locations' => [
            'model' => Location::class,
            'controller' => Locations::class,
            'request' => LocationRequest::class,
            'label' => 'lang:cupnoodles.extravars::default.model_locations',
        ],
    ];

    /** Form input name, ie. Menu[extra_vars][code] */
    public const string FORM_KEY = 'extra_vars';

    /** @var array<string, Collection<int, ExtraVar>>|null */
    protected static ?array $definitions = null;

    /** Values submitted with the admin form, held between beforeSave and afterSave. */
    protected static ?WeakMap $pending = null;

    public static function boot(): void
    {
        foreach (self::MODELS as $alias => $target) {
            self::extendModel($target['model'], $alias);
            self::extendForm($target['controller'], $alias);
        }

        // The admin only saves fields its form request validated, so add rules for ours.
        Event::listen('system.formRequest.extendValidator', function(FormRequest $request, object $dataHolder): void {
            foreach (self::MODELS as $alias => $target) {
                if ($request instanceof $target['request']) {
                    self::addValidationRules($alias, $dataHolder);
                }
            }
        });
    }

    /**
     * Extra var definitions for one record type, keyed by code. Cached for the request.
     *
     * @return Collection<string, ExtraVar>
     */
    public static function definitionsFor(string $alias): Collection
    {
        if (self::$definitions === null) {
            // Empty while the extension is installed but not yet migrated
            self::$definitions = rescue(
                fn(): array => ExtraVar::query()->orderBy('extra_var_id')->get()->groupBy('model')->all(),
                [],
                false,
            );
        }

        return collect(self::$definitions[$alias] ?? [])->keyBy('code');
    }

    public static function clearDefinitionCache(): void
    {
        self::$definitions = null;
    }

    protected static function extendModel(string $modelClass, string $alias): void
    {
        // One query per list of records instead of one per record in templates
        $modelClass::addGlobalScope('extraVarValues', function(Builder $query) use ($alias): void {
            if (self::definitionsFor($alias)->isNotEmpty()) {
                $query->with('extra_var_values');
            }
        });

        $modelClass::extend(function(Model $model) use ($alias): void {
            $model->relation['morphMany']['extra_var_values'] = [
                ExtraVarValue::class, 'name' => 'extendable', 'delete' => true,
            ];

            $model->addDynamicMethod('getExtraVarValue', fn(string $code, mixed $default = null): mixed => self::getValue($model, $alias, $code, $default));

            $model->addDynamicMethod('getExtraVars', fn(): array => self::definitionsFor($alias)
                ->map(fn(ExtraVar $definition): mixed => self::getValue($model, $alias, $definition->code))
                ->all());

            // The form posts extra_vars[...] like a column; keep it out of the INSERT/UPDATE
            // and store it once the record (and its id, on create) exists.
            $model->bindEvent('model.beforeSave', function() use ($model): void {
                $attributes = $model->getAttributes();
                if (array_key_exists(self::FORM_KEY, $attributes)) {
                    self::pending()[$model] = (array)$attributes[self::FORM_KEY];
                    unset($attributes[self::FORM_KEY]);
                    $model->setRawAttributes($attributes);
                }
            });

            $model->bindEvent('model.afterSave', function() use ($model, $alias): void {
                if (isset(self::pending()[$model])) {
                    self::saveValues($model, $alias, self::pending()[$model]);
                    unset(self::pending()[$model]);
                }
            });
        });
    }

    protected static function extendForm(string $controllerClass, string $alias): void
    {
        $controllerClass::extendFormFields(function(Form $form, $model) use ($alias): void {
            $definitions = self::definitionsFor($alias);
            if ($definitions->isEmpty() || !$model instanceof Model) {
                return;
            }

            $hasTabs = $form->getTab(FormTabs::SECTION_PRIMARY)?->hasFields() ?? false;
            $fields = [];
            foreach ($definitions as $definition) {
                $fields[self::FORM_KEY.'['.$definition->code.']'] = array_filter([
                    'label' => $definition->name,
                    'type' => ExtraVar::TYPES[$definition->type] ?? 'text',
                    'tab' => $hasTabs ? 'lang:cupnoodles.extravars::default.text_tab' : null,
                    'span' => $definition->type === 'switch' ? 'left' : null,
                    // Stored values are shown via the default, as the record has no such column
                    'default' => $model->exists ? self::getValue($model, $alias, $definition->code) : null,
                ], fn($value): bool => $value !== null);
            }

            $hasTabs ? $form->addTabFields($fields) : $form->addFields($fields);
        });
    }

    protected static function addValidationRules(string $alias, object $dataHolder): void
    {
        $definitions = self::definitionsFor($alias);
        if ($definitions->isEmpty()) {
            return;
        }

        $dataHolder->rules[self::FORM_KEY] = ['nullable', 'array'];
        foreach ($definitions as $code => $definition) {
            $key = self::FORM_KEY.'.'.$code;
            $dataHolder->rules[$key] = match ($definition->type) {
                'number' => ['nullable', 'numeric'],
                'switch' => ['nullable', 'boolean'],
                default => ['nullable', 'string', 'max:65535'],
            };
            $dataHolder->attributes[$key] = $definition->name;
        }
    }

    protected static function getValue(Model $model, string $alias, string $code, mixed $default = null): mixed
    {
        $definition = self::definitionsFor($alias)->get($code);
        if (!$definition) {
            return $default;
        }

        $stored = $model->exists
            ? $model->extra_var_values->firstWhere('extra_var_id', $definition->getKey())?->value
            : null;

        if ($stored === null && $default !== null) {
            return $default;
        }

        return $definition->castValue($stored);
    }

    protected static function saveValues(Model $model, string $alias, array $values): void
    {
        foreach (self::definitionsFor($alias) as $code => $definition) {
            if (!array_key_exists($code, $values)) {
                continue;
            }

            ExtraVarValue::updateOrCreate([
                'extra_var_id' => $definition->getKey(),
                'extendable_type' => $model->getMorphClass(),
                'extendable_id' => $model->getKey(),
            ], [
                'value' => self::normalizeValue($definition, $values[$code]),
            ]);
        }

        $model->unsetRelation('extra_var_values');
    }

    protected static function normalizeValue(ExtraVar $definition, mixed $value): ?string
    {
        $value = is_scalar($value) ? trim((string)$value) : '';

        return match ($definition->type) {
            'switch' => $value === '1' || $value === 'true' ? '1' : '0',
            'number' => is_numeric($value) ? $value : null,
            default => $value === '' ? null : $value,
        };
    }

    protected static function pending(): WeakMap
    {
        return self::$pending ??= new WeakMap;
    }
}
