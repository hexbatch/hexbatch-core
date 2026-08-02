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


        Schema::create('element_bound_in_sets', function (Blueprint $table) {
            $table->id();


            $table->foreignId('bound_set_id')
                ->nullable(false)
                ->comment("The set")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('bound_element_id')
                ->nullable(false)
                ->comment("The element in the set")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('bound_subset_id')
                ->nullable(false)
                ->comment("This is about a subset, and the element here is the definer")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->timestamp('created_at')
                ->default(DB::raw('NOW()'))
                ->comment("When created");

            $table->timestamp('updated_at')
                ->default(null)
                ->nullable()
                ->comment("When updated");



            $table->unique(['bound_set_id','bound_element_id']);

        });

        DB::statement("ALTER TABLE element_bound_in_sets
                              Add COLUMN bound_sum_shape_geom
                              geometry
                              ;
                    ");

        DB::statement("ALTER TABLE element_bound_in_sets
                              Add COLUMN bound_sum_map_geom
                              geometry
                              ;
                    ");

        DB::statement("ALTER TABLE element_bound_in_sets
                              Add COLUMN bound_sum_shape_bounding_box
                              box3d
                              ;
                    ");

        DB::statement("ALTER TABLE element_bound_in_sets
                              Add COLUMN bound_sum_map_bounding_box
                              box2d
                              ;
                    ");


        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_bound_in_sets FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement("
            CREATE OR REPLACE FUNCTION update_bounds_in_sets_geo_columns()
                RETURNS TRIGGER AS $$
            BEGIN

                NEW.bound_sum_shape_bounding_box = ST_3DExtent(NEW.bound_sum_shape_geom);
                NEW.bound_sum_map_bounding_box = ST_Extent(NEW.bound_sum_map_geom);
                RETURN NEW;
            END;
            $$ language 'plpgsql';
        ");


        DB::statement("
            CREATE TRIGGER set_bounds_in_sets_geo_before_ins BEFORE INSERT ON element_bound_in_sets FOR EACH ROW EXECUTE PROCEDURE update_bounds_in_sets_geo_columns();
        ");

        DB::statement("
            CREATE TRIGGER set_bounds_in_sets_geo_before_ups BEFORE UPDATE ON element_bound_in_sets FOR EACH ROW EXECUTE PROCEDURE update_bounds_in_sets_geo_columns();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_bound_in_sets');
    }
};
