<?php

namespace CupNoodles\ExtraVars\Models;

use Model;

/**
 * UOM Model Class
 */
class ExtraVarValues extends Model
{
    /**
     * @var string The database table name
     */
    protected $table = 'extra_var_values';

    /**
     * @var string The database table primary key
     */
    protected $primaryKey = 'extra_var_values_id';

    public $relation = [];

    protected $fillable = ['extra_vars_id', 'object_id', 'value'];

}
