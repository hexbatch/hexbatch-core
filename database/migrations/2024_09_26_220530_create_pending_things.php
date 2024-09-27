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
            //error

            $table->foreignId('thing_event_attribute_id')
                ->nullable()->default(null)
                ->comment("The attribute which represents the event")
                ->index('idx_thing_event_attribute_id')
                ->constrained('attributes')
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
            'remote',
            'stack',
            'element_creation',
            'element_batch_creation',
            'owner_change',
            'set_operation',
            'element_destruction',
            'attribute_read',
            'attribute_write',
            'set_enter',
            'set_leave',
            'set_child_created',
            'set_child_destroyed',
            'set_top_level_destroyed',
            'set_link_created',
            'set_link_destroyed',
            'server_add_element',
            'server_add_user', -- not register user, just get its token?
            'server_allowed',
            'server_removed'
            );");

        DB::statement("ALTER TABLE pending_things Add COLUMN thing_to_do type_of_thing_to_do NOT NULL default 'nothing';");



        DB::statement("CREATE TYPE type_of_thing_status AS ENUM (
            'pending',
            'ready',
            'finished',
            'error'
            );");

        DB::statement("ALTER TABLE pending_things Add COLUMN thing_status type_of_thing_status NOT NULL default 'pending';");

        Schema::table('pending_things', function (Blueprint $table) {

            $table->dateTime('status_change_at')->nullable()->default(null)
                ->comment('When the last status was made at');

            $table->jsonb('thing_value')
                ->nullable()->default(null)->comment("When something needs a value");
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
    }
};
