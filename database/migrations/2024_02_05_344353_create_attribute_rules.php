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


            $table->foreignId('owning_server_event_id')
                ->nullable(false)
                ->comment("The parent of the top level of the rule chain. Each attribute can have one rule chain")
                ->index()
                ->constrained('server_events')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('parent_rule_id')
                ->nullable()->default(null)
                ->comment("Rules can be chained")
                ->index('idx_parent_rule_id')
                ->constrained('attribute_rules')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('rule_phase_id')
                ->nullable()
                ->comment("The phase the rule tree here and below use, if null then default")
                ->index()
                ->constrained('phases')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('rule_action_or_event_type_id')
                ->nullable()->default(null)
                ->comment("The event that is being listened, or the action being done")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('rule_path_id')
                ->nullable()->default(null)
                ->comment("This rule follows a path for data and targets")
                ->index()
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->integer('rule_rank')
                ->nullable(false)->default(0)
                ->comment("orders child rules");


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
        DB::statement("CREATE TYPE type_of_logic AS ENUM (
            'nop',
            'nop_after',
            'nor_all',
            'or_all',
            'and',
            'or',
            'xor',
            'nand',
            'nor',
            'xnor',
            'always_true',
            'always_false'
            );");

        DB::statement("ALTER TABLE attribute_rules Add COLUMN child_logic type_of_logic NOT NULL default 'and';");
        DB::statement("ALTER TABLE attribute_rules Add COLUMN rule_logic type_of_logic NOT NULL default 'and';");


        DB::statement("CREATE TYPE type_of_merge_logic AS ENUM (
            'union',
            'union_newest',
            'difference',
            'union_newest_add',
            'union_add'
            'union_newest_sub',
            'union_sub'
            );");


        DB::statement("ALTER TABLE attribute_rules Add COLUMN rule_merge_method type_of_merge_logic NOT NULL default 'union';");


        DB::statement("ALTER TABLE attribute_rules ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_rules FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('attribute_rules', function (Blueprint $table) {

            $table->string('filter_json_path')
                ->nullable()->default(null)
                ->comment("if set then the data this rule node has will filter the child results to only have matching in one json to give to the parent");

            $table->string('rule_name',256)->nullable(false)
                ->comment("The name of the rule (does not have to be unique and is optional. Can also have notes");
        });


        DB::statement('ALTER TABLE attribute_rules ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_rule_parent_name ON attribute_rules (owning_server_event_id,rule_name) NULLS NOT DISTINCT;");


        DB::statement("CREATE UNIQUE INDEX idx_one_rule_root_per_tree
                                ON attribute_rules (owning_server_event_id, (parent_rule_id IS NULL)) WHERE parent_rule_id IS NULL;");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_rules');
        DB::statement("DROP TYPE type_of_logic;");
        DB::statement("DROP TYPE type_of_merge_logic;");

    }
};
