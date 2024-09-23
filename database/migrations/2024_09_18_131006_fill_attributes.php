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

            $table->foreignId('applied_rule_bundle_id')
                ->nullable()->default(null)
                ->comment("The bundle that is used, attributes do not need rules, or can inherit from parent")
                ->index('idx_applied_rule_bundle_id')
                ->constrained('attribute_rule_bundles')
                ->cascadeOnUpdate()
                ->nullOnDelete();




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

            $table->boolean('is_static')->default(false)->nullable(false)
                ->comment('if true then all elements share this static value. This is per server');

            $table->boolean('is_final')->default(false)->nullable(false)
                ->comment('if true then child types do not inherit this attribute');

            $table->boolean('is_lazy')->default(false)->nullable(false)
                ->comment('if true then elements from this type only create this attribute when written. Until then they return null');


            $table->timestamps();


            $table->text('value_json_path')->nullable()->default(null)
                ->comment("if set the value json has to match this, pointer whitelist can apply");

            $table->jsonb('attribute_value')
                ->nullable()->default(null)->comment("The value of the attribute");

            $table->string('attribute_name',128)->nullable(false)->index()
                ->comment("The unique name of the attribute, using the naming rules");


        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_type_parent_name ON attributes (owner_element_type_id,attribute_name) NULLS NOT DISTINCT;");



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
            $table->dropForeign(['applied_rule_bundle_id']);

            $table->dropColumn('parent_attribute_id');
            $table->dropColumn('owner_element_type_id');
            $table->dropColumn('applied_rule_bundle_id');
            $table->dropColumn('ref_uuid');
            $table->dropColumn('is_retired');
            $table->dropColumn('is_final');
            $table->dropColumn('is_system');
            $table->dropColumn('is_nullable');
            $table->dropColumn('is_final_parent');
            $table->dropColumn('is_using_ancestor_bundle');
            $table->dropColumn('is_static');
            $table->dropColumn('is_lazy');
            $table->dropColumn('value_json_path');
            $table->dropColumn('attribute_value');
            $table->dropColumn('attribute_name');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('server_access_type');
            $table->dropColumn('attribute_access_type');
        });

        DB::statement("DROP TYPE type_of_server_access;");
        DB::statement("DROP TYPE type_of_attribute_access;");

    }
    //todo make trigger to control recursion of the parent
};
