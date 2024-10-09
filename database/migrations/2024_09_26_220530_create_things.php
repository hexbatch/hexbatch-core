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


            //intermediate results, like collections of attributes or types found, or elements being processed, are put into thing_sets by the row that finds them
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

            $table->foreignId('api_call_type_id')
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

            $table->foreignId('thing_path_id')
                ->nullable()
                ->default(null)
                ->comment("so searches can run here")
                ->index()
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_set_id')
                ->nullable()->default(null)
                ->comment("each non trivial thing to do has a remote or stack reprented here")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            $table->foreignId('thing_type_id')
                ->nullable()->default(null)
                ->comment("When this is an event being processed")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_attribute_id')
                ->nullable()->default(null)
                ->comment("The attribute which represents the event")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('thing_element_id')
                ->nullable()->default(null)
                ->comment("When something is being done to a single element ")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("When something needs a namespace")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->timestamp('thing_start_after')->nullable()->default(null)
                ->comment('if set, then this will be done after the time, and not before');


            $table->bigInteger('thing_pagination_id')->nullable()->default(null)
                ->comment('if set, then the path will use this for paginition');

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");
        });



        DB::statement("CREATE TYPE type_of_thing_status AS ENUM (
            'thing_pending',
            'thing_success',
            'thing_error'
            );");

        DB::statement("ALTER TABLE things Add COLUMN thing_status type_of_thing_status NOT NULL default 'thing_pending';");

        DB::statement("ALTER TABLE things Add COLUMN thing_child_logic type_of_logic NOT NULL default 'and';");
        DB::statement("ALTER TABLE things Add COLUMN thing_logic type_of_logic NOT NULL default 'and';");
        DB::statement("ALTER TABLE things Add COLUMN thing_merge_method type_merge_json NOT NULL default 'overwrite';");

        Schema::table('things', function (Blueprint $table) {
            $table->index(['thing_status','thing_start_after']);

            $table->jsonb('thing_value')
                ->nullable()->default(null)->comment("When something needs a value");


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
