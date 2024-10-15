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
        Schema::create('remote_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_remote_id')
                ->nullable(false)
                ->comment("The remote this header/key is for")
                ->index('idx_activity_has_remote_uri_id')
                ->constrained('remotes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('remote_stack_id')
                ->nullable()
                ->default(null)
                ->comment("The stack this remote activity belongs to")
                ->index('idx_activity_has_remote_stack_id')
                ->constrained('remote_stacks')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('caller_action_id')
                ->nullable()
                ->default(null)
                ->comment("The action that called this remote")
                ->index('idx_activity_has_action_id')
                ->constrained('actions')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('caller_attribute_id')
                ->nullable()
                ->default(null)
                ->comment("The attribute that called this remote")
                ->index('idx_remote_activities_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('caller_user_id')
                ->nullable()
                ->default(null)
                ->comment("The user that called this remote")
                ->index('idx_remote_activities_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('caller_server_id')
                ->nullable()
                ->default(null)
                ->comment("The server this remote was called from")
                ->index('idx_remote_activities_server_id')
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('caller_element_id')
                ->nullable()
                ->default(null)
                ->comment("The element that called this remote")
                ->index('idx_remote_activities_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('caller_type_id')
                ->nullable()
                ->default(null)
                ->comment("The type that called this remote")
                ->index('idx_remote_activities_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->timestamps();
            $table->dateTime('remote_call_ended_at')->nullable()->default(null)->comment("filled in when remote completes");

        });

        DB::statement("CREATE TYPE type_of_remote_activity_status AS ENUM (
            'pending','started','success','failed','cached'
            );");

        DB::statement("ALTER TABLE remote_activities Add COLUMN remote_activity_status_type type_of_remote_activity_status NOT NULL default 'pending';");
        #--------------------------------------

        DB::statement("CREATE TYPE type_of_cache_status AS ENUM (
            'none','created','not_made','error'
            );");

        DB::statement("ALTER TABLE remote_activities Add COLUMN cache_status_type type_of_cache_status NOT NULL default 'none';");
        #--------------------------------------
         DB::statement("CREATE TYPE type_of_cache_policy AS ENUM (
            'normal','not_use_cache','use_cache_or_fail'
            );");

        DB::statement("ALTER TABLE remote_activities Add COLUMN cache_policy_type type_of_cache_policy NOT NULL default 'normal';");
        #--------------------------------------

        DB::statement('ALTER TABLE remote_activities ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');
        DB::statement("ALTER TABLE remote_activities ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remote_activities FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('remote_activities', function (Blueprint $table) {
            $table->index(['cache_status_type'],'idx_cache_status');
            $table->index(['remote_activity_status_type'],'idx_remote_status');

            $table->integer('data_priority_level_in_stack')->nullable(false)->default(0)
                ->comment("when multiple activities are run, and their data is merged, this helps in the merge strategy");

            $table->integer('response_code')->nullable()->default(null)->comment("the http status or console status");
            $table->jsonb('to_headers')->nullable()->default(null)->comment("The headers to the remote (if that kind), no secret values here");
            $table->jsonb('from_headers')->nullable()->default(null)->comment("The headers from the remote answering (if that kind), no secret values here");
            $table->jsonb('from_remote_processed_data')->nullable()->default(null)->comment("The value of going in, if marked is_secret not put here");
            $table->jsonb('to_remote_processed_data')->nullable()->default(null)->comment("The value coming back, if its json");
            $table->jsonb('to_remote_files')->nullable()->default(null)->comment("The files being sent to the remote");
            $table->jsonb('errors')->nullable()->default(null)->comment("Any errors from or to");

            $table->jsonb('consumer_passthrough_data')->nullable()->default(null)
                ->comment("This is used by any consumer who is listening to the completion event. Passthrough data");

            $table->jsonb('location_geo_json')->nullable()->default(null)
                ->comment("This is the set location bounds the call comes from, its passed to the call");

            $table->text('from_remote_raw_text')->nullable()->default(null)->comment("The value coming back, in string form");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_activities');
        DB::statement("DROP TYPE type_of_remote_activity_status");
        DB::statement("DROP TYPE type_of_cache_status");
        DB::statement("DROP TYPE type_of_cache_policy");
    }
};
