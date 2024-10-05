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
        Schema::table('paths', function (Blueprint $table) {

            $table->foreignId('path_owning_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("The optional owner of the path")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('parent_path_id')
                ->nullable()->default(null)
                ->index()
                ->comment("Paths have nested rules")
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('path_type_id')
                ->nullable()->default(null)
                ->comment("The type the path part may be about. System type for always caller type")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();




            /*
             * how can I select a ns whose home space has a type or attribute?
             * A1: get the set:home, owned by ns, containing apples.red
             *
             * Then, I was to see if this ns is not an admin of any ns I own
             * A2: me: me(owns) nor-> them(admin of ns)
             *
             * Then I was to see if the A1 and A2 intersect
             * A1 and-> A2
             *
             * top parent of children only need the logic saved, as well as
             */

            $table->foreignId('path_attribute_id')
                ->nullable()->default(null)
                ->comment("The attribute the path part may be about")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('sorting_attribute_id')
                ->nullable()->default(null)
                ->comment(" attribute to sort types and elements, use with sort_json_path. If not set then uses natural order ")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('path_element_set_id')
                ->nullable()->default(null)
                ->comment("The set this should be in.. System type for always caller set")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('path_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("When the searched must be owned by someone. Or the ns here has a relationship")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('path_location_bound_id')
                ->nullable()
                ->default(null)
                ->comment("If set contrain sets to this bound")
                ->index()
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->foreignId('path_server_id')
                ->nullable()
                ->default(null)
                ->comment("The type/element must be from this server")
                ->unique('idx_path_server_id')
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->timestamps();

            $table->integer('path_min_gap')->nullable()->default(null)
                ->comment("The min number of relationships that must exist between here and there");

            $table->integer('path_max_gap')->nullable()->default(null)
                ->comment("How max number of relationships must exist between here and there");



            $table->boolean('is_partial_matching_name')
                ->nullable(false)->default(false)
                ->comment("If false then only match full names, else wildcard on right");


            $table->boolean('is_sorting_order_asc')
                ->nullable(false)->default(false)
                ->comment("If false then desc");

            $table->integer('path_result_limit')->nullable()->default(null)
                ->comment("If set, this node will only return X results");


            $table->timestamp('path_start_at')->nullable()->default(null)
                ->comment("if set then time comparison starts here");

            $table->timestamp('path_end_at')->nullable()->default(null)
                ->comment("if set then time comparison ends here");


            $table->integer('path_min_count')->nullable()->default(null)
                ->comment("The min number of results required");

            $table->integer('path_max_count')->nullable()->default(null)
                ->comment("How max number of results required");


        });

        DB::statement('ALTER TABLE paths ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE paths ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON paths FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement("ALTER TABLE paths Add COLUMN path_logic type_of_child_logic NOT NULL default 'and';");


        //relationship with the path parent
        DB::statement("CREATE TYPE path_relationship_type AS ENUM (
            'no_relationship',
            'rule_event',
            'rule_action',
            'rule_parent',
            'rule_child',
            'owns_rule',
            'shape_intersecting',
            'shape_bordering',
            'shape_seperated',
            'intersecting_map',
            'bordering_map',
            'seperated_map',
            'time_overlapping',
            'time_distinct',
            'shares_type',
            'ancestor',
            'descendant',
            'namespace_owns_element',
            'namespace_owns_type',
            'member_of_namespace',
            'admin_of_namespace',
            'owner_of_namespace',
            'set_contains',
            'childish',
            'linkish'
            );");

        DB::statement("ALTER TABLE paths Add COLUMN path_relationship path_relationship_type NOT NULL default 'no_relationship';");



        // and a second postgres type and column for if this applies to the age of the element, how long its been in the set, the age of the type, or when the value in the set was changed
        // can use a second node for same stuff to do multiple matching for ages (example match age when element joined set and when value changed there)

        DB::statement("CREATE TYPE time_comparison_type AS ENUM (
            'no_time_comparison',
            'age_element',
            'joined_set_at',
            'age_type',
            'element_value_changed' -- dynamic or const
            );");

        DB::statement("ALTER TABLE paths Add COLUMN time_comparison time_comparison_type NOT NULL default 'no_time_comparison';");


        DB::statement("CREATE TYPE path_returns_type AS ENUM (
            'exists',
            'type',
            'element',
            'attribute',
            'namespace',
            'count'
            );");

        DB::statement("ALTER TABLE paths Add COLUMN path_returns path_returns_type NOT NULL default 'exists';");



        Schema::table('paths', function (Blueprint $table) {


            // this is the name of the attr or the type or the ns or the server
            // can use all together so total of 30*4 = 120 + 3 dots
            //if this is a uuid, then will search for this in the uuid and not the name
            $table->string('path_part_name',128)->nullable()->default(null)
                ->comment("the name of the attr or the type or the ns or the server,  for attr can use server.ns.type.attribute");



            $table->string('value_json_path')
                ->nullable()->default(null)
                ->comment("if set then only values that match the json path are used");

            $table->string('sort_json_path')
                ->nullable()->default(null)
                ->comment("if set then the values are ordered by this. Not valid past a certain result set size");

            $table->text('path_compiled_sql')
                ->nullable()->default(null)
                ->comment("Stores sql this path was converted to, the parents will include the children");

        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_path_parent_name ON paths (parent_path_id,path_part_name) NULLS NOT DISTINCT;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER update_modified_time ON paths");

        Schema::table('paths', function (Blueprint $table) {
            $table->dropForeign(['path_owning_namespace_id']);
            $table->dropForeign(['parent_path_id']);
            $table->dropForeign(['path_type_id']);
            $table->dropForeign(['path_attribute_id']);
            $table->dropForeign(['sorting_attribute_id']);
            $table->dropForeign(['path_element_set_id']);
            $table->dropForeign(['path_namespace_id']);
            $table->dropForeign(['path_location_bound_id']);
            $table->dropForeign(['path_server_id']);

            $table->dropColumn('path_location_bound_id');
            $table->dropColumn('path_server_id');
            $table->dropColumn('path_namespace_id');
            $table->dropColumn('parent_path_id');
            $table->dropColumn('path_type_id');
            $table->dropColumn('path_attribute_id');
            $table->dropColumn('sorting_attribute_id');
            $table->dropColumn('path_element_set_id');
            $table->dropColumn('path_owning_namespace_id');

            $table->dropColumn('ref_uuid');
            $table->dropColumn('path_min_gap');
            $table->dropColumn('path_max_gap');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('path_logic');
            $table->dropColumn('path_relationship');
            $table->dropColumn('path_returns');
            $table->dropColumn('path_part_name');
            $table->dropColumn('is_partial_matching_name');
            $table->dropColumn('path_start_at');
            $table->dropColumn('path_end_at');
            $table->dropColumn('time_comparison');
            $table->dropColumn('sort_json_path');
            $table->dropColumn('path_min_count');
            $table->dropColumn('path_max_count');
            $table->dropColumn('value_json_path');
            $table->dropColumn('is_sorting_order_asc');
            $table->dropColumn('path_result_limit');
            $table->dropColumn('path_compiled_sql');
        });
        DB::statement("DROP TYPE path_relationship_type;");
        DB::statement("DROP TYPE time_comparison_type;");
        DB::statement("DROP TYPE path_returns_type;");
    }
};
