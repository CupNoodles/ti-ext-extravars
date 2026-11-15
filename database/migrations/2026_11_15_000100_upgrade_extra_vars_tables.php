<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * v4 structure:
 * - extra_vars: slug -> code, class ('Menu') -> model (morph alias 'menus'), type ('String') -> 'text'
 * - extra_var_values: polymorphic (extendable_type/extendable_id) instead of a bare object_id,
 *   value becomes text, one value per field per record
 *
 * Values whose field no longer exists are deleted: v3 left them behind when a field was deleted,
 * and they can't be shown or read anywhere.
 */
return new class extends Migration
{
    protected array $models = ['Menu' => 'menus', 'Category' => 'categories', 'Location' => 'locations'];

    protected array $types = ['String' => 'text', 'Int' => 'number', 'Bool' => 'switch'];

    public function up(): void
    {
        Schema::table('extra_vars', function(Blueprint $table): void {
            $table->renameColumn('extra_vars_id', 'extra_var_id');
            $table->renameColumn('slug', 'code');
            $table->renameColumn('class', 'model');
        });

        Schema::table('extra_vars', function(Blueprint $table): void {
            $table->timestamps();
        });

        foreach ($this->models as $old => $new) {
            DB::table('extra_vars')->where('model', $old)->update(['model' => $new]);
        }

        foreach ($this->types as $old => $new) {
            DB::table('extra_vars')->where('type', $old)->update(['type' => $new]);
        }

        $this->makeCodesUnique();

        Schema::table('extra_vars', function(Blueprint $table): void {
            $table->unique(['model', 'code']);
        });

        Schema::table('extra_var_values', function(Blueprint $table): void {
            $table->renameColumn('extra_var_values_id', 'extra_var_value_id');
            $table->renameColumn('extra_vars_id', 'extra_var_id');
        });

        Schema::table('extra_var_values', function(Blueprint $table): void {
            $table->unsignedInteger('extra_var_id')->change();
            $table->string('extendable_type')->nullable()->after('extra_var_id');
            $table->unsignedBigInteger('extendable_id')->nullable()->after('extendable_type');
            $table->text('value')->nullable()->change();
        });

        DB::table('extra_var_values')->whereNotIn('extra_var_id', DB::table('extra_vars')->select('extra_var_id'))->delete();

        DB::table('extra_var_values')
            ->join('extra_vars', 'extra_vars.extra_var_id', '=', 'extra_var_values.extra_var_id')
            ->update([
                'extra_var_values.extendable_type' => DB::raw('extra_vars.model'),
                'extra_var_values.extendable_id' => DB::raw('CAST(extra_var_values.object_id AS UNSIGNED)'),
            ]);

        $this->removeDuplicateValues();

        Schema::table('extra_var_values', function(Blueprint $table): void {
            $table->dropColumn('object_id');
        });

        Schema::table('extra_var_values', function(Blueprint $table): void {
            $table->string('extendable_type')->nullable(false)->change();
            $table->unsignedBigInteger('extendable_id')->nullable(false)->change();
            $table->unique(['extra_var_id', 'extendable_type', 'extendable_id'], 'extra_var_values_unique');
            $table->index(['extendable_type', 'extendable_id'], 'extra_var_values_extendable_index');
        });
    }

    public function down(): void
    {
        Schema::table('extra_var_values', function(Blueprint $table): void {
            $table->dropUnique('extra_var_values_unique');
            $table->dropIndex('extra_var_values_extendable_index');
            $table->string('object_id')->nullable()->after('extra_var_id');
        });

        DB::table('extra_var_values')->update(['object_id' => DB::raw('extendable_id')]);

        Schema::table('extra_var_values', function(Blueprint $table): void {
            $table->dropColumn(['extendable_type', 'extendable_id']);
            $table->string('object_id')->nullable(false)->change();
            $table->string('value')->nullable()->change();
            $table->integer('extra_var_id')->change();
        });

        Schema::table('extra_var_values', function(Blueprint $table): void {
            $table->renameColumn('extra_var_value_id', 'extra_var_values_id');
            $table->renameColumn('extra_var_id', 'extra_vars_id');
        });

        Schema::table('extra_vars', function(Blueprint $table): void {
            $table->dropUnique(['model', 'code']);
            $table->dropTimestamps();
        });

        foreach ($this->models as $old => $new) {
            DB::table('extra_vars')->where('model', $new)->update(['model' => $old]);
        }

        foreach ($this->types as $old => $new) {
            DB::table('extra_vars')->where('type', $new)->update(['type' => $old]);
        }

        Schema::table('extra_vars', function(Blueprint $table): void {
            $table->renameColumn('extra_var_id', 'extra_vars_id');
            $table->renameColumn('code', 'slug');
            $table->renameColumn('model', 'class');
        });
    }

    /**
     * v3 didn't stop two fields on the same record type sharing a slug (only the first was ever read).
     * Give later duplicates a numbered code so the unique index can be added.
     */
    protected function makeCodesUnique(): void
    {
        $seen = [];
        foreach (DB::table('extra_vars')->orderBy('extra_var_id')->get() as $row) {
            $key = $row->model.'|'.$row->code;
            if (!isset($seen[$key])) {
                $seen[$key] = true;

                continue;
            }

            for ($i = 2; isset($seen[$row->model.'|'.$row->code.'_'.$i]); $i++);
            $seen[$row->model.'|'.$row->code.'_'.$i] = true;
            DB::table('extra_vars')->where('extra_var_id', $row->extra_var_id)->update(['code' => $row->code.'_'.$i]);
        }
    }

    /**
     * Keep the newest value when a record somehow has two for the same field.
     */
    protected function removeDuplicateValues(): void
    {
        $keep = DB::table('extra_var_values')
            ->selectRaw('MAX(extra_var_value_id) as id')
            ->groupBy('extra_var_id', 'extendable_type', 'extendable_id')
            ->pluck('id');

        DB::table('extra_var_values')->whereNotIn('extra_var_value_id', $keep)->delete();
    }
};
