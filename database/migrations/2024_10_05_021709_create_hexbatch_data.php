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
        Schema::create('hexbatch_data', function (Blueprint $table) {
            $table->id();


            $table->foreignId('collection_attribute_id')
                ->nullable()->default(null)
                ->comment("Data has an attribute ")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_type_id')
                ->nullable()->default(null)
                ->comment("Data has a type")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_set_id')
                ->nullable()->default(null)
                ->comment("Data has a set")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_element_id')
                ->nullable()->default(null)
                ->comment("Data has an element")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_set_member_id')
                ->nullable()->default(null)
                ->comment("Data has a set member")
                ->index()
                ->constrained('element_set_members')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('collection_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("Data has a namespace")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_path_id')
                ->nullable()
                ->default(null)
                ->comment("Data has a path")
                ->index()
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_user_id')
                ->nullable()
                ->default(null)
                ->comment("Data has a user")
                ->index()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_cursor')
                ->nullable(false)->default(false)
                ->comment("If true what is in this row is the cursor for the path of the thing for the next page");


            $table->jsonb('collection_json')
                ->nullable()->default(null)->comment("Data has json");
        });

        DB::statement("CREATE TYPE type_of_thing_data_source AS ENUM (
                'not_set',
                'caller_info',
                'from_children',
                'from_current',
                'run_time_data',
                'from_action_setup',
                'from_api_setup');");

        DB::statement("ALTER TABLE hexbatch_data Add COLUMN thing_data_source type_of_thing_data_source NOT NULL default 'not_set';");

        DB::statement("ALTER TABLE hexbatch_data ADD CONSTRAINT chk_one_thing_set CHECK (
            num_nonnulls(collection_attribute_id  ,collection_type_id, collection_namespace_id, collection_element_id, collection_set_id) = 1)
        ;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hexbatch_data');
        DB::statement("DROP TYPE type_of_thing_data_source;");

    }
};
