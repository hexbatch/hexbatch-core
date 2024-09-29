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

            $table->foreignId('parent_rule_id')
                ->nullable()->default(null)
                ->comment("Rules can be chained")
                ->index('idx_parent_rule_id')
                ->constrained('attribute_rules')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('target_path_id')
                ->nullable()->default(null)
                ->comment("This rule follows a path")
                ->index('idx_target_path_id')
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('trigger_path_id')
                ->nullable()->default(null)
                ->comment("This rule follows a path")
                ->index('idx_trigger_path_id')
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_path_id')
                ->nullable()->default(null)
                ->comment("This rule follows a path")
                ->index('idx_data_path_id')
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('rule_trigger_remote_type_id')
                ->nullable()->default(null)
                ->comment("If this rule calling a remote then put this here")
                ->index('idx_rule_trigger_remote_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");


            $table->integer('rule_weight')->nullable(false)->default(1)
                ->comment("when combined with others of its type, how important is this?");

            $table->integer('rule_value')->nullable(false)->default(1)
                ->comment("summed with others of its type, if total <= 0 rule is not applied");

            $table->jsonb('rule_constant_data')->nullable()->default(null)
                ->comment("if no data attribute this is used. if remote this is used as input to the remote");


            $table->timestamps();
        });




         DB::statement("CREATE TYPE type_of_rule_trigger_action AS ENUM (
            'exists',
            'not_exist'
            );");


       DB::statement("ALTER TABLE attribute_rules Add COLUMN attribute_trigger_action type_of_rule_trigger_action NOT NULL default 'exists';");



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

        DB::statement("CREATE TYPE rule_data_action_type AS ENUM (
            'no_action',
            'pragma_read_bounds_time', -- convert to json and write to target
            'pragma_read_bounds_shape',
            'pragma_read_bounds_map',
            'pragma_read_bounds_path',
            'read'
            );");

        DB::statement("ALTER TABLE attribute_rules Add COLUMN rule_data_action rule_data_action_type NOT NULL default 'no_action';");

        DB::statement("CREATE TYPE rule_target_action_type AS ENUM (
            'no_action',
            'pragma_facet_offset',
            'pragma_facet_rotation',
            'pragma_element_on',
            'pragma_element_toggle',
            'pragma_element_off',
            'pragma_element_type_on',
            'pragma_element_type_toggle',
            'pragma_element_type_off',
            'command_make_set',
            'command_destroy_set',
            'command_add_to_set',
            'command_change_set',
            'command_destroy_user', -- server user
            'command_assign_user_to_empty', -- server user
            'command_create_element', -- single only
            'command_destroy_element',
            'command_group_add_member', -- group found by the type of the attribute chosen
            'command_group_add_admin',
            'command_group_remove_member',
            'command_group_remove_admin',
            'type_attribute_required',
            'set_membership_affinity',
            'write'
            );");

        DB::statement("ALTER TABLE attribute_rules Add COLUMN target_action rule_target_action_type NOT NULL default 'no_action';");


        DB::statement("CREATE TYPE type_merge_json AS ENUM (
            'overwrite',
            'or_merge',
            'and_merge',
            'xor_merge'
            );");



        DB::statement("ALTER TABLE attribute_rules Add COLUMN target_writing_method type_merge_json NOT NULL default 'overwrite';");


        DB::statement("ALTER TABLE attribute_rules ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_rules FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('attribute_rules', function (Blueprint $table) {
            $table->string('rule_name',128)->nullable(false)->index()
                ->comment("The unique name of the rule in the bundle, using the naming rules");
        });


        DB::statement('ALTER TABLE attribute_rules ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_rules');
        DB::statement("DROP TYPE type_of_rule_trigger_action;");
        DB::statement("DROP TYPE type_of_child_logic;");
        DB::statement("DROP TYPE rule_target_action_type;");
        DB::statement("DROP TYPE type_merge_json;");
        DB::statement("DROP TYPE rule_data_action_type;");

    }
};
