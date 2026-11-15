<?php

declare(strict_types=1);

namespace CupNoodles\ExtraVars\Models;

use Igniter\Flame\Database\Model;

/**
 * The value of one extra field on one record.
 *
 * @property int $extra_var_value_id
 * @property int $extra_var_id
 * @property string $extendable_type
 * @property int $extendable_id
 * @property string|null $value
 */
class ExtraVarValue extends Model
{
    protected $table = 'extra_var_values';

    protected $primaryKey = 'extra_var_value_id';

    public $timestamps = false;

    protected $fillable = ['extra_var_id', 'extendable_type', 'extendable_id', 'value'];

    protected $casts = [
        'extra_var_id' => 'integer',
        'extendable_id' => 'integer',
    ];

    public $relation = [
        'belongsTo' => [
            'extra_var' => [ExtraVar::class, 'foreignKey' => 'extra_var_id'],
        ],
        'morphTo' => [
            'extendable' => [],
        ],
    ];
}
