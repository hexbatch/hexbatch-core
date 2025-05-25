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
        Schema::create('action_data', function (Blueprint $table) {
            $table->id();


            $table->foreignId('parent_data_id')
                ->nullable()->default(null)
                ->comment("If this is a child")
                ->index()
                ->constrained('action_data')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('root_data_id')
                ->nullable()
                ->default(null)
                ->comment("all things in the same tree, including the root, have this set to the root")
                ->index()
                ->constrained('action_data')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_namespace_owner_id')
                ->nullable()->default(null)
                ->comment("Data is owned by a namespace")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_type_owner_id')
                ->nullable()->default(null)
                ->comment("Data is used by a type in an action")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('data_attribute_id')
                ->nullable()->default(null)
                ->comment("Data has an attribute ")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_second_attribute_id')
                ->nullable()->default(null)
                ->comment("Data has a second attribute in main operation ")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_third_attribute_id')
                ->nullable()->default(null)
                ->comment("Data has a third attribute in main operation ")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_type_id')
                ->nullable()->default(null)
                ->comment("Data has a type")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_second_type_id')
                ->nullable()->default(null)
                ->comment("Data has a second type in main operation")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_set_id')
                ->nullable()->default(null)
                ->comment("Data has a set")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_second_set_id')
                ->nullable()->default(null)
                ->comment("Data has another set in the main operation")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_element_id')
                ->nullable()->default(null)
                ->comment("Data has an element")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_second_element_id')
                ->nullable()->default(null)
                ->comment("Data has another element in the main operation")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_set_member_id')
                ->nullable()->default(null)
                ->comment("Data has a set member")
                ->index()
                ->constrained('element_set_members')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('data_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("Data has a namespace")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_path_id')
                ->nullable()
                ->default(null)
                ->comment("Data has a path")
                ->index()
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_user_id')
                ->nullable()
                ->default(null)
                ->comment("Data has a user")
                ->index()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_server_id')
                ->nullable()
                ->default(null)
                ->comment("Data has a server")
                ->index()
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_mutual_id')
                ->nullable()
                ->default(null)
                ->comment("Data has a mutual")
                ->index()
                ->constrained('mutuals')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_phase_id')
                ->nullable()
                ->default(null)
                ->comment("Data has a phase")
                ->index()
                ->constrained('phases')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('data_second_phase_id')
                ->nullable()
                ->default(null)
                ->comment("Data has another phase in the main operation")
                ->index()
                ->constrained('phases')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_system_privilege')->default(false)->nullable(false) //
                ->comment('if true then this action runs in higher privilege');

            $table->boolean('is_sending_events')->default(false)->nullable(false)
                ->comment('if true then this action does not send events');

            $table->integer('data_priority')->default(0)->nullable(false)
                ->comment('sets the priority of the action and thus thing');

            $table->timestamps();

            $table->jsonb('collection_data')
                ->nullable()->default(null)->comment("Other data here");


            $table->jsonb('data_tags')
                ->nullable()->default(null)
                ->comment("array of string tags for the action to feed the things");


        });

        DB::statement("ALTER TABLE action_data Add COLUMN action_status type_of_thing_status NOT NULL default 'thing_pending';");

        DB::statement("ALTER TABLE action_data ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON action_data FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_data');

    }
};
