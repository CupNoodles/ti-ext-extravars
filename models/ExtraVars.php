<?php

namespace CupNoodles\ExtraVars\Models;

use Model;

/**
 * UOM Model Class
 */
class ExtraVars extends Model
{
    /**
     * @var string The database table name
     */
    protected $table = 'extra_vars';

    /**
     * @var string The database table primary key
     */
    protected $primaryKey = 'extra_vars_id';

    public $relation = [];


}
