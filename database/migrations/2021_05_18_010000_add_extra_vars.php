<?php

namespace CupNoodles\ExtraVars\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Schema;

/**
 * 
 */
class AddExtraVars extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('extra_vars')) {
            Schema::create('extra_vars', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('extra_vars_id');
                $table->string('name');
                $table->string('slug');
                $table->string('type');
                $table->string('class');
            });
        }

        if (!Schema::hasTable('extra_var_values')) {
            Schema::create('extra_var_values', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('extra_var_values_id');
                $table->integer('extra_vars_id');
                $table->string('object_id');
                $table->string('value')->nullable();
            });
        }

    }

    public function down()
    {

        Schema::dropIfExists('extra_vars');
        Schema::dropIfExists('extra_var_values');

    }

}
