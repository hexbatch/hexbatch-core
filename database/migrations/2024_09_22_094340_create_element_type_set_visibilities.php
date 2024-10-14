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

            $table->foreignId('visibility_set_id')
                ->nullable(false)
                ->comment("The set this type is matched for")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_visible_for_map')->default(true)->nullable(false)
                ->comment('if true this is visible because the set overlaps the type map');

            $table->boolean('is_visible_for_time')->default(true)->nullable(false)
                ->comment('if true this is visible after a time check');

            $table->boolean('is_time_sensitive')->default(false)->nullable(false)
                ->comment('if true this should be checked next tick');


            $table->timestamps();

            $table->unique(['visible_type_id','visibility_set_id']);
        });

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
