<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Original v3 tables. Kept as-is so existing installs keep their migration history;
 * 2026_11_15_000100_upgrade_extra_vars_tables brings them to the v4 structure.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('extra_vars')) {
            Schema::create('extra_vars', function(Blueprint $table): void {
                $table->engine = 'InnoDB';
                $table->increments('extra_vars_id');
                $table->string('name');
                $table->string('slug');
                $table->string('type');
                $table->string('class');
            });
        }

        if (!Schema::hasTable('extra_var_values')) {
            Schema::create('extra_var_values', function(Blueprint $table): void {
                $table->engine = 'InnoDB';
                $table->increments('extra_var_values_id');
                $table->integer('extra_vars_id');
                $table->string('object_id');
                $table->string('value')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_var_values');
        Schema::dropIfExists('extra_vars');
    }
};
