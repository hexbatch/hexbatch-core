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

        Schema::create('attribute_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rule_bundle_owner_id')
                ->nullable(false)
                ->comment("The bundle that owns this rule")
                ->index('idx_rule_bundle_owner_id')
                ->constrained('attribute_rule_bundles')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('parent_rule_id')
                ->nullable()->default(null)
                ->comment("Rules can be chained")
                ->index('idx_parent_rule_id')
                ->constrained('attribute_rules')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('rule_trigger_attribute_id')
                ->nullable()->default(null)
                ->comment("If this rule is depending on an attribute,this can be a stand alone, or ancestor, affecting all decendants")
                ->index('idx_rule_trigger_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('rule_target_attribute_id')
                ->nullable()->default(null)
                ->comment("The target of the rule, this can be descendants or not")
                ->index('idx_rule_target_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_attribute_id')
                ->nullable()->default(null)
                ->comment("If doing a write, use this. If not set and a write, then only works if target is nullable, then set to null")
                ->index('idx_data_source_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->integer('trigger_descendant_range')->default(0)->nullable(false)
                ->comment('default means do not use descenands, otherwise how many generations, positive only');

            $table->integer('target_descendant_range')->default(0)->nullable(false)
                ->comment('default means do not use descenands, otherwise how many generations, positive only');

            $table->integer('data_descendant_range')->default(0)->nullable(false)
                ->comment('default means do not use descenands, otherwise how many generations, positive only');

            $table->integer('scope_range_target')->default(0)->nullable(false)
                ->comment('how many parent sets or child sets to range');

            $table->integer('scope_range_trigger')->default(0)->nullable(false)
                ->comment('how many parent sets or child sets to range');


            $table->integer('rule_weight')->nullable(false)->default(1)
                ->comment("when combined with others of its type, how important is this?");

            $table->integer('rule_value')->nullable(false)->default(1)
                ->comment("summed with others of its type, if total <= 0 rule is not applied");

            $table->text('rule_json_path')->nullable()->default(null)
                ->comment("if set matches path to the the target json value");



            $table->timestamps();
        });



        DB::statement("CREATE TYPE type_of_rule AS ENUM (
            'inactive',
            'required',
            'set_membership_affinity'
            'set_toggle_affinity',
            'action'
            );");
        /* Affinity membership depends on elements in a set to decide to join it when asked by command,
                 Affinity toggle can turn an attribute to not be readable or writable (both at the same time) in a set based on the contents
                 and the required is build time, so no checking there
                */


        DB::statement("ALTER TABLE attribute_rules Add COLUMN rule_type type_of_rule NOT NULL default 'inactive';");



         DB::statement("CREATE TYPE type_of_rule_trigger_action AS ENUM (
            'no_trigger',
            'exists',
            'not_exist',
            'turned_off'
            'turned_on',
            'set_created'
            'set_destroyed'
            );");
         /*
         make set, just ignore if set already exists
         destroy set ( element is still there, just has no set), contents popped out to parent, can only destroy if such set has a parent
          */

       DB::statement("ALTER TABLE attribute_rules Add COLUMN attribute_trigger_action type_of_rule_trigger_action NOT NULL default 'no_trigger';");





        DB::statement("CREATE TYPE type_of_rule_target AS ENUM (
            'target_attribute',
            'type_of_target_attribute',
            'set_of_attribute'
            );");

        DB::statement("ALTER TABLE attribute_rules Add COLUMN rule_target_scope type_of_rule_target NOT NULL default 'target_attribute';");


        DB::statement("CREATE TYPE type_of_rule_target_scope AS ENUM (
            'same_set',
            'parent_set',
            'child_set'
            );");

        DB::statement("ALTER TABLE attribute_rules Add COLUMN rule_set_scope_target type_of_rule_target_scope NOT NULL default 'same_set';");
        DB::statement("ALTER TABLE attribute_rules Add COLUMN rule_set_scope_trigger type_of_rule_target_scope NOT NULL default 'same_set';");


        DB::statement("CREATE TYPE type_of_rule_restriction AS ENUM (
            'own_type',
            'other_type',
            'all'
            );");

        DB::statement("ALTER TABLE attribute_rules Add COLUMN rule_restriction_trigger type_of_rule_restriction NOT NULL default 'own_type';");
        DB::statement("ALTER TABLE attribute_rules Add COLUMN rule_restriction_target type_of_rule_restriction NOT NULL default 'own_type';");
        DB::statement("ALTER TABLE attribute_rules Add COLUMN rule_restriction_data type_of_rule_restriction NOT NULL default 'own_type';");



        DB::statement("CREATE TYPE type_of_rule_quantity AS ENUM (
            'one',
            'all'
            );");

        DB::statement("ALTER TABLE attribute_rules Add COLUMN scope_quantity_target type_of_rule_quantity NOT NULL default 'one';");



        DB::statement("CREATE TYPE type_of_child_logic AS ENUM (
            'and',
            'or',
            'xor',
            'nand',
            'nor',
            'xnor',
            'always_true',
            'always_false'
            );");

        DB::statement("ALTER TABLE attribute_rules Add COLUMN child_logic type_of_child_logic NOT NULL default 'and';");


        DB::statement("CREATE TYPE rule_target_action_type AS ENUM (
            'no_action',
            'toggle',
            'off',
            'on',
            'write',
            'make_set',
            'destroy_set'
            );");

        DB::statement("ALTER TABLE attribute_rules Add COLUMN target_action rule_target_action_type NOT NULL default 'no_action';");


        DB::statement("CREATE TYPE rule_target_write_type AS ENUM (
            'none',
            'overwrite',
            'or_merge',
            'and_merge',
            'xor_merge'
            );");



        DB::statement("ALTER TABLE attribute_rules Add COLUMN target_writing_method rule_target_write_type NOT NULL default 'none';");



        DB::statement("ALTER TABLE attribute_rules ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_rules FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('attribute_rules', function (Blueprint $table) {
            $table->string('rule_name',128)->nullable(false)->index()
                ->comment("The unique name of the rule in the bundle, using the naming rules");
        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_rule_parent_name ON attribute_rules (rule_bundle_owner_id,rule_name) NULLS NOT DISTINCT;");

        DB::statement('ALTER TABLE attribute_rules ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_rules');
        DB::statement("DROP TYPE type_of_rule;");
        DB::statement("DROP TYPE type_of_rule_trigger_action;");
        DB::statement("DROP TYPE type_of_rule_target;");
        DB::statement("DROP TYPE type_of_rule_target_scope;");
        DB::statement("DROP TYPE type_of_rule_restriction;");
        DB::statement("DROP TYPE type_of_rule_quantity;");
        DB::statement("DROP TYPE type_of_child_logic;");
        DB::statement("DROP TYPE rule_target_action_type;");
        DB::statement("DROP TYPE rule_target_write_type;");

    }
};
