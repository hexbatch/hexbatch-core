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

            $table->foreignId('shape_set_member_id')
                ->nullable(false)
                ->comment("The element/set this intersecting bounds is about")
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

            $table->integer('shape_z_order_for_events')
                ->nullable(false)->default(0)
                ->comment("Higher z levels have set wide events, scoped to the shape, called first");

        });

        DB::statement("CREATE TYPE type_of_shape_intersection AS ENUM ('design', 'live');");

        DB::statement("ALTER TABLE attribute_shape_intersections Add COLUMN kind_shape_intersection type_of_shape_intersection NOT NULL default 'design';");
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
