<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attribute_shape_intersections', function (Blueprint $table) {
            $table->id();
            // make note to pop the attribute intersection if live type is removed OR the attribute is hidden by live type
            $table->foreignId('parent_type_intersection_id')
                ->nullable(false)
                ->comment("The set element and type this is about.")
                ->index()
                ->constrained('element_type_intersections')
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

        });

               DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_shape_x_per_combo ON attribute_shape_intersections
            (parent_type_intersection_id,shape_entry_attribute_id,parent_type_intersection_id) NULLS DISTINCT;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('attribute_shape_intersections');
    }
};
