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

        Schema::create('attribute_rule_nodes', function (Blueprint $table) {
            $table->id();


            $table->foreignId('owning_rule_id')
                ->nullable(false)
                ->comment("The rule that owns these parts.")
                ->index()
                ->constrained('attribute_rules')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('parent_rule_node_id')
                ->nullable()->default(null)
                ->comment("Rules can be chained")
                ->index('idx_parent_rule_id')
                ->constrained('attribute_rule_nodes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('rule_event_type_id')
                ->nullable()->default(null)
                ->comment("The event that is being listened")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('rule_path_id')
                ->nullable()->default(null)
                ->comment("This rule follows a path for data,trigger and targets")
                ->index()
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");




            $table->timestamps();
        });





        //child rules return a boolean value , as well as data or results they found. The child logic will combine the children,
        // the rule_logic will combine this node with the calculated child truthfulness
        /*
         * false:
         *   no results
         *   json with success at top level set to false
         *   empty json
         *   impossible to run (no target found, no event fired)
         */
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

        DB::statement("ALTER TABLE attribute_rule_nodes Add COLUMN rule_child_logic type_of_child_logic NOT NULL default 'and';");
        DB::statement("ALTER TABLE attribute_rule_nodes Add COLUMN rule_logic type_of_child_logic NOT NULL default 'and';");


        DB::statement("CREATE TYPE type_merge_json AS ENUM (
            'no_merge',
            'overwrite',
            'or_merge',
            'and_merge',
            'xor_merge'
            'oldest'
            'newest'
            );");


        DB::statement("ALTER TABLE attribute_rule_nodes Add COLUMN rule_merge_method type_merge_json NOT NULL default 'overwrite';");


        DB::statement("ALTER TABLE attribute_rule_nodes ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_rule_nodes FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('attribute_rule_nodes', function (Blueprint $table) {

            $table->string('filter_json_path')
                ->nullable()->default(null)
                ->comment("if set then the data this rule node has will filter the child results to only have matching in one json to give to the parent");

            $table->string('rule_node_name',256)->nullable()->default(null)->index()
                ->comment("The name of the rule (does not have to be unique and is optional. Can also have notes");
        });
        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_rule_node_name ON attribute_rule_nodes (owning_rule_id,rule_node_name) NULLS NOT DISTINCT;");

        DB::statement('ALTER TABLE attribute_rule_nodes ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_rule_nodes');
        DB::statement("DROP TYPE type_of_child_logic;");
        DB::statement("DROP TYPE type_merge_json;");

    }
};
