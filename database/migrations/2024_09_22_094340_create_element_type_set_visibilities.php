<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
/*
             * table to have the type and set, where an element is at
             *  and if the type is visible in the set, then have the element-values link to that instead of the horde
             *  when element leaves remove from new table.
             *  element_type_set_visibilities
             * set_id, type_id, bool visible_map, bool visible_time, bool time sensitive
             * when looking to update time visibility, this will help update that.
             * Need to limit time change window to X minutes (in config) to allow proper updating, with promise of +- a minute or so
             */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('element_type_set_visibilities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visible_type_id')
                ->nullable(false)
                ->comment("The type that may be visible for this set, or not")
                ->index()
                ->constrained('element_types')
                ->cascadeOnDelete()
                ->cascadeOnDelete();

            //make this visible_set_member_id nullable when its top level set visibility by type
            $table->foreignId('visible_set_member_id')
                ->nullable()
                ->comment("The set this type is matched for, null for top level set")
                ->index()
                ->constrained('element_set_members')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->rawColumn('is_visible','boolean GENERATED ALWAYS AS (is_visible_for_location AND is_visible_for_schedule AND is_turned_on) STORED')
                ->comment("calculated boolean that is true when all the other columns are true");


            $table->boolean('is_visible_for_location')->default(true)->nullable(false)
                ->comment('if true this is visible because the set overlaps the type map');

            $table->boolean('is_visible_for_schedule')->default(true)->nullable(false)
                ->comment('if true this is visible after a time check');


            $table->boolean('is_turned_on')->default(true)->nullable(false)
                ->comment('if false then all the attributes are off for this type in this set context');


            $table->timestamps();

            $table->unique(['visible_type_id','visible_set_member_id']);
        });

        DB::statement(
            "CREATE UNIQUE INDEX udx_visible_type_member ON element_type_set_visibilities
                    (visible_type_id,visible_set_member_id) NULLS NOT DISTINCT;");

        DB::statement("ALTER TABLE element_type_set_visibilities ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_type_set_visibilities FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_set_visibilities');
    }
};
