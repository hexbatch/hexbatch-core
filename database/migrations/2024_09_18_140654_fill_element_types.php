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
        Schema::table('element_types', function (Blueprint $table) {

            $table->foreignId('owner_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the type")
                ->index('idx_type_owner_namespace_id')
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('imported_from_server_id')
                ->nullable()
                ->default(null)
                ->comment("If imported from another server")
                ->index()
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();



            $table->foreignId('type_time_bound_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a time bounds.")
                ->index('idx_type_time_bound_id')
                ->constrained('time_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('type_location_map_bound_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a location map bounds")
                ->index('idx_type_location_map_bound_id')
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('type_description_element_id')
                ->nullable()
                ->default(null)
                ->comment("This is an optional description/hook element")
                ->unique('udx_type_description_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->timestamps();

            DB::statement("CREATE TYPE type_of_lifecycle AS ENUM (
                'developing',
                'published',
                'retired', -- these can be toggled any time back and forth
                'suspended' -- these can be toggled any time back and forth
            );");

            DB::statement("ALTER TABLE element_types Add COLUMN lifecycle type_of_lifecycle NOT NULL default 'developing';");


            $table->boolean('is_system')->default(false)->nullable(false)
                ->index('idx_type_is_system')
                ->comment('if true then this attribute is a standard attribute');

            $table->boolean('is_final_type')->default(false)->nullable(false)
                ->comment('if true then cannot be added as parent');

        });

        //todo add type_sum_geom_map for attributes that have a map
        DB::statement("ALTER TABLE element_types
                              Add COLUMN type_sum_geom_shape
                              geometry
                              ;
                    ");


        Schema::table('element_types', function (Blueprint $table) {
            $table->string('type_name',128)->nullable(false)->index()
                ->comment("The unique name of the type, using the naming rules");
        });

        DB::statement('ALTER TABLE element_types ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE element_types ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_types FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_user_per_type_name ON element_types (owner_namespace_id,type_name) NULLS NOT DISTINCT;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER update_modified_time ON element_types");

        Schema::table('element_types', function (Blueprint $table) {
            $table->dropForeign(['owner_namespace_id']);
            $table->dropForeign(['imported_from_server_id']);
            $table->dropForeign(['type_time_bound_id']);
            $table->dropForeign(['type_location_map_bound_id']);
            $table->dropForeign(['type_description_element_id']);

            $table->dropColumn('owner_namespace_id');
            $table->dropColumn('imported_from_server_id');
            $table->dropColumn('type_time_bound_id');
            $table->dropColumn('type_location_map_bound_id');
            $table->dropColumn('type_description_element_id');

            $table->dropColumn('type_name');
            $table->dropColumn('ref_uuid');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('is_system');
            $table->dropColumn('is_final_type');
            $table->dropColumn('type_sum_geom_shape');
            $table->dropColumn('lifecycle');



        });

        DB::statement("DROP TYPE type_of_lifecycle;");
    }
};
