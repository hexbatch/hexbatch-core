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
        //min info: owner_element_type_id, and set attribute_name to null
        Schema::table('attributes', function (Blueprint $table) {

            $table->foreignId('owner_element_type_id')
                ->nullable(false)
                ->comment("The type that owns this attribute")
                ->index('idx_attribute_owner_element_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('parent_attribute_id')
                ->nullable()
                ->default(null)
                ->comment("The optional parent of the attribute")
                ->index('idx_attribute_parent_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('design_attribute_id')
                ->nullable()
                ->default(null)
                ->comment("Optional attribute, does not have to be on the type, whose shape is shown when the attribute is shown.")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();





            $table->foreignId('attribute_shape_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a location shape bounds")
                ->index()
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");



            $table->boolean('is_system')->default(false)->nullable(false)
                ->index()
                ->comment('if true then this attribute is a standard attribute');



            $table->boolean('is_final_attribute')->default(false)->nullable(false)
                ->comment('if true then cannot be used as a parent');

            $table->boolean('is_public_domain')->default(false)->nullable(false)
                ->comment('if true then anyone can use without asking');

            $table->boolean('is_abstract')->default(false)->nullable(false)
                ->comment('if true then child must have attribute that inherits from this');




        });



        DB::statement('ALTER TABLE attributes ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');







        DB::statement("CREATE TYPE type_of_server_access AS ENUM (
            'is_private',
            'is_public',
            'is_protected'
            );");

        DB::statement("ALTER TABLE attributes Add COLUMN server_access_type type_of_server_access NOT NULL default 'is_private';");





        DB::statement("CREATE TYPE type_of_element_value_policy AS ENUM (
            'static',        -- same across the server for all elements that use it
            'per_element',   -- can have different values in each element that uses it
            'per_set_chain', -- can have different values for each element's membership in each grouping of sets
            'per_set'        -- can have different values for each element's membership in each set

            );");

        DB::statement("ALTER TABLE attributes Add COLUMN value_policy type_of_element_value_policy NOT NULL default 'static';");


        DB::statement("CREATE TYPE type_of_approval AS ENUM (
            'approval_not_set',
            'pending_design_approval',
            'design_approved',
            'design_denied',
            'pending_publishing_approval',
            'publishing_approved',
            'publishing_denied'
            );");

        DB::statement("ALTER TABLE attributes Add COLUMN attribute_approval type_of_approval NOT NULL default 'approval_not_set';");


        Schema::table('attributes', function (Blueprint $table) {

            $table->timestamps();

            $table->rawColumn('read_json_path','jsonpath')->nullable()->default(null)
                ->comment("if set the value given is filtered by this");

            $table->rawColumn('validate_json_path','jsonpath')->nullable()->default(null)
                ->comment("if set the value has to match this before being written");

            $table->string('attribute_name',128)->nullable()->index()
                ->comment("The unique name of the attribute, using the naming rules");
        });

        DB::statement("ALTER TABLE attributes ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attributes FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_type_parent_name ON attributes (owner_element_type_id,attribute_name) NULLS NOT DISTINCT;");
    } //up

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER update_modified_time ON attributes");

        Schema::table('attributes', function (Blueprint $table) {
            $table->dropForeign(['parent_attribute_id']);
            $table->dropForeign(['design_attribute_id']);
            $table->dropForeign(['owner_element_type_id']);
            $table->dropForeign(['attribute_shape_id']);

            $table->dropColumn('parent_attribute_id');
            $table->dropColumn('design_attribute_id');
            $table->dropColumn('owner_element_type_id');
            $table->dropColumn('attribute_shape_id');
            $table->dropColumn('ref_uuid');
            $table->dropColumn('is_final_attribute');
            $table->dropColumn('is_abstract');
            $table->dropColumn('is_public_domain');
            $table->dropColumn('is_system');
            $table->dropColumn('read_json_path');
            $table->dropColumn('attribute_name');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('server_access_type');
            $table->dropColumn('value_policy');
            $table->dropColumn('attribute_approval');

        });

        DB::statement("DROP TYPE type_of_server_access;");
        DB::statement("DROP TYPE type_of_element_value_policy;");
        DB::statement("DROP TYPE type_of_approval;");

    }
};
