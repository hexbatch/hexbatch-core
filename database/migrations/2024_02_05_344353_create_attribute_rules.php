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
            // todo add parent attribute (removing that rule link in the attribute)
            //  attr is only set when the rule has no rule parent, and this is a unique column to ensure only one rule chain per attr
            //  when attr is inherited, the parent rule is run before the child rule, going back to the root ancestor
            //  events have no children, and the to-do only looks at the top rules for the attrs involved, to start

            //todo add event type for listening to events

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


            //todo remove the remote, its now a command and uses the target which is a remote
            $table->foreignId('rule_remote_type_id')
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


        //todo remove this, too hard to implement the not exist
         DB::statement("CREATE TYPE rule_trigger_action_type AS ENUM (
            'exists',
            'not_exist'
            );");


       DB::statement("ALTER TABLE attribute_rules Add COLUMN attribute_trigger_action rule_trigger_action_type NOT NULL default 'exists';");



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

        //todo all data action, and target action are s types representing mini api

        //todo add pragma type to read the defining attribute or combine all the type data into one json
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
            'pragma_shape_offset',
            'pragma_shape_rotation',
            'pragma_shape_color',
            'pragma_shape_texture',
            'pragma_shape_opacity',
            'pragma_shape_zorder',
            'pragma_element_on',
            'pragma_element_toggle',
            'pragma_element_off',
            'pragma_element_type_on',
            'pragma_element_type_toggle',
            'pragma_element_type_off',
            'command_run_remote',
            'command_make_set',
            'command_destroy_set',
            'command_add_to_set',
            'command_change_set',
            'command_destroy_namespace', -- server user or ns owner
            'command_destroy_user', -- server user
            'command_assign_user_to_namespace', -- server user
            'command_create_element', -- single only
            'command_destroy_element',
            'command_add_live_type_element', -- type(s) found in the data path
            'command_remove_live_type_element',
            'command_namespace_add_member', -- namespace found by the type of the attribute chosen,server user or ns owner
            'command_namespace_add_admin',
            'command_namespace_remove_member',
            'command_namespace_remove_admin',
            'type_attribute_required',
            'membership_affinity',
            'read',
            'write'
            );");

        DB::statement("ALTER TABLE attribute_rules Add COLUMN target_action rule_target_action_type NOT NULL default 'no_action';");


        //todo add ignore, oldest, newest (if no time difference or cannot tell defaults to overwrite)
        DB::statement("CREATE TYPE type_merge_json AS ENUM (
            'overwrite',
            'or_merge',
            'and_merge',
            'xor_merge'
            );");

        //todo if have children, add a merge strategy between all the children and the data here, null means not using children data

        DB::statement("ALTER TABLE attribute_rules Add COLUMN target_writing_method type_merge_json NOT NULL default 'overwrite';");


        DB::statement("ALTER TABLE attribute_rules ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_rules FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('attribute_rules', function (Blueprint $table) {
            $table->string('rule_name',256)->nullable()->default(null)->index()
                ->comment("The name of the rule (does not have to be unique and is optional. Can also have notes");
        });


        DB::statement('ALTER TABLE attribute_rules ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_rules');
        DB::statement("DROP TYPE rule_trigger_action_type;");
        DB::statement("DROP TYPE type_of_child_logic;");
        DB::statement("DROP TYPE rule_target_action_type;");
        DB::statement("DROP TYPE type_merge_json;");
        DB::statement("DROP TYPE rule_data_action_type;");

    }
};
