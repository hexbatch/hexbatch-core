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
        Schema::create('pending_things', function (Blueprint $table) {
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







            $table->foreignId('parent_thing_id')
                ->nullable()->default(null)
                ->comment("If this is a child")
                ->index('idx_parent_thing_id')
                ->constrained('pending_things')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('api_call_type_id')
                ->nullable()->default(null)
                ->comment("When api is made, its type is put here")
                ->index('idx_thing_api_call_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('caller_user_type_id')
                ->nullable()->default(null)
                ->comment("When api is made, the logged in user type")
                ->index('idx_thing_caller_user_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_event_attribute_id')
                ->nullable()->default(null)
                ->comment("The attribute which represents the event")
                ->index('idx_thing_event_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('thing_rule_id')
                ->nullable()->default(null)
                ->comment("Which rule made the row")
                ->index('idx_thing_rule_id')
                ->constrained('attribute_rules')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_call_result_set_id')
                ->nullable()->default(null)
                ->comment("each non trivial thing to do has a remote or stack reprented here")
                ->index('idx_thing_call_result_set_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            $table->foreignId('thing_one_set_id')
                ->nullable()->default(null)
                ->comment("When a source set A is needed")
                ->index('idx_thing_one_set_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_two_set_id')
                ->nullable()->default(null)
                ->comment("when a source set B is needed")
                ->index('idx_thing_two_set_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_destination_set_id')
                ->nullable()->default(null)
                ->comment("When an operation is going to put something in the set (operation or joining a set)  ")
                ->index('idx_thing_destination_set_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_type_id')
                ->nullable()->default(null)
                ->comment("When something is needing type info")
                ->index('idx_thing_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('filter_type_id')
                ->nullable()->default(null)
                ->comment("When a filter is used for set operations")
                ->index('idx_filter_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_element_id')
                ->nullable()->default(null)
                ->comment("When something is being done to a single element ")
                ->index('idx_thing_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('group_aggregate_source_attribute_id')
                ->nullable()->default(null)
                ->comment("The attribute which has aggregation")
                ->index('idx_group_aggregate_source_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_element_values_id')
                ->nullable()->default(null)
                ->comment("for read or write after, or for aggregation results")
                ->index('idx_thing_element_values_id')
                ->constrained('element_values')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->foreignId('filter_set_id')
                ->nullable()->default(null)
                ->comment("for dynamic filtering")
                ->index('idx_thing_filter_set_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_paths')
                ->nullable()
                ->default(null)
                ->comment("so searches can run here")
                ->index('idx_thing_paths_id')
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();





            $table->foreignId('thing_user_type_id')
                ->nullable()
                ->default(null)
                ->comment("When something needs a user")
                ->index('idx_thing_user_type_id')
                ->constrained('user_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_server_type_id')
                ->nullable()
                ->default(null)
                ->comment("When something needs a server")
                ->index('idx_thing_server_type_id')
                ->constrained('user_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('thing_hex_error_id')
                ->nullable()
                ->default(null)
                ->comment("When something goes wrong")
                ->index('idx_thing_hex_error_id')
                ->constrained('hex_errors')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->timestamps();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");
        });



        DB::statement("CREATE TYPE type_of_thing_to_do AS ENUM (

            'nothing',

            'attribute_read',
            'attribute_write',

            'attribute_turned_off',
            'attribute_turned_on',

            'type_attributes_turned_off',
            'type_attributes_turned_on',


            'element_creation',
            'element_batch_creation',
            'element_destruction',

            'group_operation',


            'remote',
            'stack',

            'search_results',


            'set_operation',
            'set_enter',
            'set_leave',
            'set_contents_shape_changed',
            'set_transport',
            'set_kick',
            'set_created',
            'set_child_created',
            'set_destroyed',
            'set_child_destroyed',
            'set_top_level_destroyed',
            'set_link_created',
            'set_link_destroyed',


            'server_add_element',
            'server_add_type',
            'server_process_event',
            'server_add_set',
            'server_remove_element',
            'server_remove_type',
            'server_remove_set',
            'server_run_rules',
            'server_read',
            'server_write',
            'server_get_user_token',
            'server_add_remote_user', -- after user token given
            'server_user_regenerate_key',
            'server_remove_remote_user',
            'server_created',
            'server_allowed',
            'server_removed',
            'server_after_removed',
            'server_paused',
            'server_regenerate_key',

            'server_sent_callback_server_regenerated_key',
            'server_sent_callback_user_regenerated_key',
            'server_sent_callback_got_user_token',
            'server_sent_callback_removed_remote_user',
            'server_sent_callback_create',
            'server_sent_callback_destroy',
            'server_sent_callback_run_rules',
            'server_sent_callback_read',
            'server_sent_callback_write',
            'server_sent_callback_element_add',
            'server_sent_callback_element_request',
            'server_sent_callback_element_remove',
            'server_sent_callback_process_event',
            'server_sent_callback_set_add',
            'server_sent_callback_ask_user_permission',


             'shape_intersection_enter',
             'shape_intersection_leave',
             'shape_bordering_attached',
             'shape_bordering_seperated',

            -- grandchildren further decendants trigger both events in this area
            'type_attribute_parent_add',
            'type_parent_add',
            'type_created_before',
            'type_updated_before',
            'type_created_after',
            'type_updated_after',

            -- servers that user inherits can listen to these below
             'user_add_before',
             'user_remove_before',
             'user_add_after',
             'user_remove_after',
             'user_owner_change',


             'user_group_member_add',
             'user_group_admin_add',
             'user_admin_removing_member',
             'user_owner_removing_admin'


            );");

        DB::statement("ALTER TABLE pending_things Add COLUMN thing_to_do type_of_thing_to_do NOT NULL default 'nothing';");



        DB::statement("CREATE TYPE type_of_thing_status AS ENUM (
            'pending',
            'finished_approved',
            'finished_denied',
            'error'
            );");

        DB::statement("ALTER TABLE pending_things Add COLUMN thing_status type_of_thing_status NOT NULL default 'pending';");



        DB::statement("CREATE TYPE type_user_followup AS ENUM (
            'nothing',
            'direct',
            'polled',
            'callback_successful',
            'callback_error'
            );");

        DB::statement("ALTER TABLE pending_things Add COLUMN user_followup type_user_followup NOT NULL default 'nothing';");


        DB::statement("CREATE TYPE type_filter_set_usage AS ENUM (
            'none',
            'must_match',
            'must_exclude'
            );");

        DB::statement("ALTER TABLE pending_things Add COLUMN filter_set_usage type_filter_set_usage NOT NULL default 'none';");



        Schema::table('pending_things', function (Blueprint $table) {

            $table->dateTime('status_change_at')->nullable()->default(null)
                ->comment('When the last status was made at');

            $table->integer('callback_http_status')->nullable()->default(null)
                ->comment('When the callback was made, what was the code returned');



            $table->jsonb('thing_value')
                ->nullable()->default(null)->comment("When something needs a value");

            $table->string('callback_url')->nullable()->default(null)
                ->comment('If set, this will be called with the result or error');

            // group operations can used chained things here, the result set for children are the source set for the parent
            $table->string('group_operation_name')->nullable()->default(null)
                ->comment('php constants used here');
        });


        DB::statement('ALTER TABLE pending_things ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE pending_things ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON pending_things FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_things');
        DB::statement("DROP TYPE type_of_thing_to_do;");
        DB::statement("DROP TYPE type_of_thing_status;");
        DB::statement("DROP TYPE type_user_followup;");
        DB::statement("DROP TYPE type_filter_set_usage;");
    }
};
