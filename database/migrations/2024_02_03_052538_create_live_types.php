<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Live types are the only way to filter events, and once the live type if off or muted, then the event is no longer filtered
       Down-set cannot undo a type applied but can apply an earlier type
     *
     */
    public function up(): void
    {
        Schema::create('live_types', function (Blueprint $table) {
            $table->id();


            $table->foreignId('live_phase_id')
                ->nullable(false)
                ->comment("The phase this belongs to")
                ->index()
                ->constrained('phases')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('live_target_element_id')
                ->nullable(false)
                ->comment("The element the live type is applied to")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('live_applied_type_id')
                ->nullable(false)
                ->comment("The the type being applied")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('live_applied_in_set_id')
                ->nullable(false)
                ->comment("The set this took place at, down-set will be affected")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('masking_live_id')
                ->nullable()->default(null)
                ->comment("When the same element has another live type applied downset from its orginal application")
                ->index()
                ->constrained('live_types')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->timestamps();
        });

        DB::statement("ALTER TABLE live_types
                              Add COLUMN live_sum_shape_geom
                              geometry
                              ;
                    ");

        DB::statement("ALTER TABLE live_types
                              Add COLUMN live_sum_map_geom
                              geometry
                              ;
                    ");

        DB::statement("ALTER TABLE live_types
                              Add COLUMN live_sum_shape_bounding_box
                              box3d
                              ;
                    ");

        DB::statement("ALTER TABLE live_types
                              Add COLUMN live_sum_map_bounding_box
                              box2d
                              ;
                    ");

        DB::statement('ALTER TABLE live_types ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE live_types ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON live_types FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement("
            CREATE OR REPLACE FUNCTION update_live_types_geo_columns()
                RETURNS TRIGGER AS $$
            BEGIN

                NEW.live_sum_shape_bounding_box = ST_3DExtent(NEW.live_sum_shape_geom);
                NEW.live_sum_map_bounding_box = ST_Extent(NEW.live_sum_map_geom);
                RETURN NEW;
            END;
            $$ language 'plpgsql';
        ");

        DB::statement("
            CREATE TRIGGER set_live_geo_before_ins BEFORE INSERT ON live_types FOR EACH ROW EXECUTE PROCEDURE update_live_types_geo_columns();
        ");

        DB::statement("
            CREATE TRIGGER set_live_geo_before_ups BEFORE UPDATE ON live_types FOR EACH ROW EXECUTE PROCEDURE update_live_types_geo_columns();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_types');
        DB::statement("DROP FUNCTION IF EXISTS update_live_types_geo_columns();");
    }
};
