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

            //todo remove this
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

            //todo rm ancestor bundle
            $table->boolean('is_using_ancestor_bundle')->default(false)->nullable(false)
                ->comment('if false then if this has parent, not using its rules');

            $table->boolean('is_system')->default(false)->nullable(false)
                ->index('idx_attr_is_system')
                ->comment('if true then this attribute is a standard attribute');

            //todo rm nullable, put rules in json_path for value
            $table->boolean('is_nullable')->default(true)->nullable(false)
                ->comment('if true then value is nullable');

            //todo remove is const
            $table->boolean('is_const')->default(false)->nullable(false)
                ->comment('if true then all elements share this static value. This is per server');

            $table->boolean('is_final')->default(false)->nullable(false)
                ->comment('if true then child types do not inherit this attribute. But this can be used as a parent in an attribute in the child');

            //todo rm is_protected_read,is_protected_write
            $table->boolean('is_protected_read')->default(false)->nullable(false)
                ->comment('if true then only ns members/element owners can read');

            $table->boolean('is_protected_write')->default(false)->nullable(false)
                ->comment('if true then only ns members/element owners can write');

            //todo enum set_value_behavior for how it behaves going into child sets, and how it returns from child sets when that child is destroyed
            // values are static (def), per_child (only when put into a child), per_set (for all being different), per_all
            // rm this
            $table->boolean('is_per_set_value')->default(false)->nullable(false)
                ->comment('if true then the element value of this is different for each set');



            $table->timestamps();







        });

        //todo add new column for merge live (used for the merging B to the A on the type that is the mergee)
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
            'element_private', -- membership of the element owner can read
            'element_private_admin',  -- only the admin in  ns that owns this element can read
            'type_private', -- membership group of ns that owns this type can read
            'type_private_admin' -- only the admin in  ns that owns this type can read
            );");

        DB::statement("ALTER TABLE attributes Add COLUMN attribute_access_type type_of_attribute_access NOT NULL default 'normal';");

        //todo add in enum and col for if this is namespace protected read or write or both, none (def)

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
        //todo add type of encryption_policy: none, namespace encrypts via rules, server encrypts via rules
        //todo add type of merging for re-entry of previously exported elements that are sent back later to the server
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
            $table->dropColumn('is_protected_read');
            $table->dropColumn('is_protected_write');
            $table->dropColumn('value_json_path');
            $table->dropColumn('attribute_value');
            $table->dropColumn('attribute_name');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('server_access_type');
            $table->dropColumn('attribute_access_type');
            $table->dropColumn('popped_writing_method');
            $table->dropColumn('attribute_shape_display');
        });

        DB::statement("DROP TYPE type_of_server_access;");
        DB::statement("DROP TYPE type_of_attribute_access;");

    }
};
