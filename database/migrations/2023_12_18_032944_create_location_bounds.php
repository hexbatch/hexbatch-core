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
        Schema::create('location_bounds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_attribute_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the bound")
                ->index('idx_location_bound_owner_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_retired')->default(false)->nullable(false)
                ->comment('if true then cannot be added to token types or make new tokens');

        });

        DB::statement("CREATE TYPE type_of_location AS ENUM ('map', 'shape');");

        DB::statement("ALTER TABLE location_bounds Add COLUMN location_type type_of_location NOT NULL default 'map';");

        Schema::table('location_bounds', function (Blueprint $table) {
            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");
        });


        DB::statement("ALTER TABLE location_bounds
                              Add COLUMN geom
                              geometry
                              ;
                    ");

        Schema::table('location_bounds', function (Blueprint $table) {
            $table->jsonb('geo_json')->comment("the original json that is used to make this geom");
            $table->timestamps();

            $table->string('bound_name',128)->nullable(false)->index()
                ->comment("The unique name of the location bound, using the naming rules");
        });

        DB::statement('ALTER TABLE location_bounds ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE location_bounds ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON location_bounds FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement("
            CREATE OR REPLACE FUNCTION update_location_bounds_geo_column()
                RETURNS TRIGGER AS $$
            BEGIN
                NEW.geom = ST_AsText(ST_GeomFromGeoJSON(New.geo_json));
                RETURN NEW;
            END;
            $$ language 'plpgsql';
        ");

        DB::statement("
            CREATE TRIGGER set_geo_before_ins BEFORE INSERT ON location_bounds FOR EACH ROW EXECUTE PROCEDURE update_location_bounds_geo_column();
        ");

        DB::statement("
            CREATE TRIGGER set_geo_before_ups BEFORE UPDATE ON location_bounds FOR EACH ROW EXECUTE PROCEDURE update_location_bounds_geo_column();
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('location_bounds');
        DB::statement("DROP TYPE type_of_location;");
        DB::statement("DROP FUNCTION IF EXISTS update_location_bounds_geo_column();");
    }
};
