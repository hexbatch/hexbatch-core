<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    //todo there can be either location bounds, but each attribute can have a map or shape to it
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attribute_shape_intersections', function (Blueprint $table) {
            $table->id();
            // make note to pop the attribute intersection if live type is removed OR the attribute is hidden by live type
            //todo new column for the parent type intersection which can be a design type or live type
            $table->foreignId('shape_set_member_id')
                ->nullable()
                ->comment("The element/set this intersecting bounds is about. Can be nullable if ns admin for both and type live")
                ->index()
                ->constrained('element_set_members')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('shape_entry_attribute_id')
                ->nullable(false)
                ->comment("The first attribute of the pair. This is the new arrival in the set (or turned back on)")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('shape_exist_attribute_id')
                ->nullable(false)
                ->comment("The second attribute of the pair. This was already existing in the set")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            //todo add new column for the type of intersection (shape or map), use location type
        });

        DB::statement("CREATE TYPE type_of_shape_intersection AS ENUM ('designed_shape', 'live_shape');");

        DB::statement("ALTER TABLE attribute_shape_intersections Add COLUMN kind_shape_intersection type_of_shape_intersection NOT NULL default 'designed_shape';");
        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_shape_x_per_combo ON attribute_shape_intersections (shape_set_member_id,shape_entry_attribute_id,shape_exist_attribute_id) NULLS DISTINCT;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('attribute_shape_intersections');
        DB::statement("DROP TYPE type_of_shape_intersection;");
    }
};
