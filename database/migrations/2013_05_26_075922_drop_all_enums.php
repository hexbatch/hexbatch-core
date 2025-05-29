<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP FUNCTION IF EXISTS update_modified_column();");
        DB::statement("DROP FUNCTION IF EXISTS update_location_bounds_geo_column();");
        DB::statement("DROP FUNCTION IF EXISTS update_live_types_geo_columns();");
        DB::statement("DROP FUNCTION IF EXISTS update_type_geo_columns();");

        DB::statement("DROP TYPE IF EXISTS type_of_location;");
        DB::statement("DROP TYPE IF EXISTS type_of_live_attribute_behavior;");
        DB::statement("DROP TYPE IF EXISTS type_of_server_event_access;");
        DB::statement("DROP TYPE IF EXISTS type_of_logic;");
        DB::statement("DROP TYPE IF EXISTS type_of_merge_logic;");
        DB::statement("DROP TYPE IF EXISTS type_of_server_access;");
        DB::statement("DROP TYPE IF EXISTS type_of_element_value_policy;");
        DB::statement("DROP TYPE IF EXISTS type_of_approval;");
        DB::statement("DROP TYPE IF EXISTS type_of_lifecycle;");
        DB::statement("DROP TYPE IF EXISTS type_of_path_status;");
        DB::statement("DROP TYPE IF EXISTS type_of_path_relationship;");
        DB::statement("DROP TYPE IF EXISTS type_of_time_comparison;");
        DB::statement("DROP TYPE IF EXISTS type_of_path_returns;");
        DB::statement("DROP TYPE IF EXISTS type_of_server_status;");
        DB::statement("DROP TYPE IF EXISTS type_of_intersection_category;");
        DB::statement("DROP TYPE IF EXISTS type_of_live_rule_policy;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //does nothing going down
    }
};
