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
        Schema::create('things', function (Blueprint $table) {
            $table->id();
            //filter what is readable or writable first before here
            // when an event returns, then process parent if all children are done
            // if no remotes, then run immediately
            // remotes when finishing will call the code to complete the stack or individual remote,
            // the rule pragma thing_update will update the result , and the pragma call will start the evaluation,
            // the thing uuid is stored as a value in the attribute that has the rule to the pragma, so that is passed here for lookup
            // all remotes and sets are processed by queue for each remote call, the data for the remote calls are in the remote element
            // all remote elements are also put into standard sets for pending, completed, failed

            //when an api call is made that can possibly toggle events,
            // the parent has the api type and the user type, and  has the rest of the data set also
            // the request data is set in the top parent json data
            // when no children (no events) or all children ready, then the call is made (switch statement with api type guid and what to call)


            //intermediate results, like collections of attributes or types found, or elements being processed, are put into thing_data by the row that finds them
            /*
             * in thing:
             *  parent thing
             *  api type  (includes mini api for rules, regular api, and events)
             *  rule  (the rule this is from)
             *  path
             *  context set (also caller set, and sources, destination set for the operation)
             *  context type (when doing a filtering by type, caller type at the very top)
             *  context attribute (when doing aggregation)
             *  context element (when a single element is the target, is the caller element at the very top)
             *  context namespace (when members or admin stuff, is the caller namespace at the very top, this also marks the outside servers that are talking)
             *  context json (the input at the top, or else is data found)
             *  type_of_thing_status
             */


            $table->foreignId('parent_thing_id')
                ->nullable()->default(null)
                ->comment("If this is a child")
                ->index()
                ->constrained('things')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('after_thing_id')
                ->nullable()->default(null)
                ->comment("runs after the parent, which is then a child to any leaf nodes of the after tree")
                ->index()
                ->constrained('things')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('api_or_action_type_id')
                ->nullable()->default(null)
                ->comment("When api is made, its type is put here")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_rule_id')
                ->nullable()->default(null)
                ->comment("Which rule made the row")
                ->index()
                ->constrained('attribute_rules')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->foreignId('thing_phase_id')
                ->nullable()
                ->comment("The phase this data will have, if null then default")
                ->index()
                ->constrained('phases')
                ->cascadeOnUpdate()
                ->restrictOnDelete();



            $table->timestamps();

            $table->timestamp('thing_start_after')->nullable()->default(null)
                ->comment('if set, then this will be done after the time, and not before');

            $table->timestamp('thing_invalid_at')->nullable()->default(null)
                ->comment('if set, then this thing will return false to its parent if the time its processed is after');

            $table->boolean('is_waiting_on_hook')->default(false)->nullable(false)
                ->comment('if true then look at the hook cluster table for pending hook returns');

            $table->smallInteger('debugging_breakpoint')
                ->nullable(false)->default(0)
                ->comment("when breakpoint set for the debugger in the row. Do not need a hook to pause here");


            $table->smallInteger('thing_rank')
                ->nullable(false)->default(0)
                ->comment("orders child rules");



            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");
        });



        DB::statement("CREATE TYPE type_of_thing_status AS ENUM (
            'thing_pending',
            'thing_waiting', -- on mutex or semaphore
            'thing_paused', -- to stop auto running of things, when the debugger is single stepping or breakpoint
            'thing_success',
            'thing_error'
            );");

        DB::statement("ALTER TABLE things Add COLUMN thing_status type_of_thing_status NOT NULL default 'thing_pending';");

        DB::statement("ALTER TABLE things Add COLUMN thing_child_logic type_of_logic NOT NULL default 'and';");
        DB::statement("ALTER TABLE things Add COLUMN thing_logic type_of_logic NOT NULL default 'and';");
        DB::statement("ALTER TABLE things Add COLUMN thing_merge_method type_merge_json NOT NULL default 'overwrite';");

        Schema::table('things', function (Blueprint $table) {
            $table->index(['thing_status','thing_start_after']);
        });


        DB::statement('ALTER TABLE things ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE things ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON things FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('things');
        DB::statement("DROP TYPE type_of_thing_status;");

    }
};
