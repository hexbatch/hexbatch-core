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

        Schema::table('attributes', function (Blueprint $table) {

            $table->foreignId('owner_element_type_id')
                ->nullable()
                ->default(null)
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

            $table->foreignId('applied_rule_id')
                ->nullable()->default(null)
                ->comment("The single or root rule used here")
                ->index('idx_applied_rule_id')
                ->constrained('attribute_rules')
                ->cascadeOnUpdate()
                ->nullOnDelete();



            $table->foreignId('attribute_location_shape_bound_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a location shape bounds")
                ->index('idx_attribute_location_shape_bound_id')
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->boolean('is_retired')->default(false)->nullable(false)
                ->index('idx_is_retired')
                ->comment('if true then cannot be added as parent or added to anything');


            $table->boolean('is_final_parent')->default(false)->nullable(false)
                ->comment('if true then cannot be used as a parent');

            $table->boolean('is_using_ancestor_bundle')->default(false)->nullable(false)
                ->comment('if false then if this has parent, not using its rules');

            $table->boolean('is_system')->default(false)->nullable(false)
                ->index('idx_attr_is_system')
                ->comment('if true then this attribute is a standard attribute');

            $table->boolean('is_nullable')->default(true)->nullable(false)
                ->comment('if true then value is nullable');

            $table->boolean('is_const')->default(false)->nullable(false)
                ->comment('if true then all elements share this static value. This is per server');

            $table->boolean('is_final')->default(false)->nullable(false)
                ->comment('if true then child types do not inherit this attribute. But this can be used as a parent in an attribute in the child');

            $table->boolean('is_per_set_value')->default(false)->nullable(false)
                ->comment('if true then the element value of this is different for each set');

            $table->timestamp('when_const_value_changed')->default(null)->nullable()
                ->comment('Updated when the value is updated here, otherwise null');

            $table->timestamps();







        });

        DB::statement("ALTER TABLE attributes Add COLUMN popped_writing_method type_merge_json NOT NULL default 'overwrite';");






        DB::statement('ALTER TABLE attributes ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE attributes ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attributes FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");




        DB::statement("CREATE TYPE type_of_server_access AS ENUM (
            'public',
            'private_to_home_server',
            'whitelisted_servers',
            'whitelisted_servers_read_only',
            'other_servers_read_only'
            );");

        DB::statement("ALTER TABLE attributes Add COLUMN server_access_type type_of_server_access NOT NULL default 'private_to_home_server';");

        DB::statement("CREATE TYPE type_of_attribute_access AS ENUM (
            'normal',
            'element_private',
            'type_private'
            );");

        DB::statement("ALTER TABLE attributes Add COLUMN attribute_access_type type_of_attribute_access NOT NULL default 'normal';");

        Schema::table('attributes', function (Blueprint $table) {
            $table->jsonb('attribute_value')
                ->nullable()->default(null)->comment("The value of the attribute");

            $table->jsonb('attribute_shape_display')
                ->nullable()->default(null)->comment("The value of the attribute");


            $table->text('value_json_path')->nullable()->default(null)
                ->comment("if set the value json has to match this, pointer whitelist can apply");

            $table->string('attribute_name',128)->nullable(false)->index()
                ->comment("The unique name of the attribute, using the naming rules");
        });

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
            $table->dropForeign(['owner_element_type_id']);
            $table->dropForeign(['attribute_location_shape_bound_id']);
            $table->dropForeign(['applied_rule_id']);

            $table->dropColumn('parent_attribute_id');
            $table->dropColumn('owner_element_type_id');
            $table->dropColumn('attribute_location_shape_bound_id');
            $table->dropColumn('applied_rule_id');
            $table->dropColumn('ref_uuid');
            $table->dropColumn('is_retired');
            $table->dropColumn('is_final');
            $table->dropColumn('is_system');
            $table->dropColumn('is_nullable');
            $table->dropColumn('is_final_parent');
            $table->dropColumn('is_using_ancestor_bundle');
            $table->dropColumn('is_const');
            $table->dropColumn('is_per_set_value');
            $table->dropColumn('value_json_path');
            $table->dropColumn('attribute_value');
            $table->dropColumn('attribute_name');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('server_access_type');
            $table->dropColumn('attribute_access_type');
            $table->dropColumn('popped_writing_method');
            $table->dropColumn('attribute_shape_display');
            $table->dropColumn('when_const_value_changed');
        });

        DB::statement("DROP TYPE type_of_server_access;");
        DB::statement("DROP TYPE type_of_attribute_access;");

    }
};
