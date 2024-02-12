<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
  user: required
        name : unique in urls
        usage_group: (optional)
        is_retired: default false // if true then cannot be added to element types
        is_on : if off then all read and writes will fail and the remote not called
        timeout_seconds: if an attempt is made to sent to the remote, this is how many seconds until the read or write of the attribute ends in failure

        uri:
            uri_type: (none,url,port,console,manual)
            uri_method (post, get, patch.. etc)
            uri_port:
            uri_string
            uri_to_remote_format
            uri_from_remote_format

        cache:
            is_caching: bool, if true then each last call updates the cache, and if same cache param key values then cache is used
            cache_ttl_seconds: how old the cache is allowed to be
            cache_keys: array of string keys to use for the cache comparisons, empty means no comparison

        read_policy:
            allow: bool

        write_policy:
            allow: bool,
        data:
            from_remote_map: array<rule to convert data from the server to value in (attr or action)>
            to_remote_map: array<rule to convert either pre-set value, or data in (attr or action) to some part of a data format to the server>
            is_sending_context_to_remote: bool
        call_schedule:
            call_max_per_unit: x
            call_unit_in_seconds: x


Remotes:
    * id
    * remote_user_id
    * is_retired
    * is_on
    * timeout_seconds
    * enum uri_type
    * enum uri_method
    * uri_string
    * uri_port
    * is_caching
    * cache_ttl_seconds
    * jsonb cache_keys
    * jsonb input_attribute_map
    * jsonb output_map
    call_max_per_unit
    call_unit_in_seconds
    remote_name

 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('remotes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the remote")
                ->index('idx_remote_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('usage_group_id')
                ->nullable()->default(null)
                ->comment("Optional user group this is about")
                ->index('idx_remotes_usage_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('action_event_id')
                ->nullable()->default(null)
                ->comment("When the remote type is action_event")
                ->index('idx_remotes_action_event_id')
                ->constrained('action_events')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('search_path_id')
                ->nullable()->default(null)
                ->comment("When the remote type is action_event")
                ->index('idx_remotes_search_path_id')
                ->constrained('search_paths')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->boolean('is_retired')->default(false)->nullable(false)
                ->comment('if true then cannot be added to attributes or actions');

            $table->boolean('is_on')->default(true)->nullable(false)
                ->comment('if false then cannot call remote');

            $table->integer('timeout_seconds')->default(null)->nullable()
                ->comment('if set, this is the total time a remote can take before the read or write on the attribute fails');

            $table->timestamps();
        });
        #------------------------------
        DB::statement("CREATE TYPE type_remote_uri AS ENUM (
            'none','url','socket','console','manual','action_event'
            );");

        DB::statement("ALTER TABLE remotes Add COLUMN uri_type type_remote_uri  NOT NULL default 'none';");
        #------------------------------

        DB::statement("CREATE TYPE type_remote_uri_method AS ENUM (
            'none','post','get','put','patch','delete'
            );");

        DB::statement("ALTER TABLE remotes Add COLUMN uri_method_type type_remote_uri_method  NOT NULL default 'none';");
        #------------------------------

        DB::statement("CREATE TYPE type_remote_data_format AS ENUM (
            'none','plain_text','xml','json'
            );");

        DB::statement("ALTER TABLE remotes Add COLUMN uri_to_remote_format type_remote_data_format  NOT NULL default 'none';");
        DB::statement("ALTER TABLE remotes Add COLUMN uri_from_remote_format type_remote_data_format  NOT NULL default 'none';");
        #------------------------------

        DB::statement('ALTER TABLE remotes ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE remotes ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remotes FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('remotes', function (Blueprint $table) {
            $table->string('uri_string')->nullable(false)->comment("The url, socket command, console command or hint of the manual call");

            $table->integer('uri_port')->default(null)->nullable()
                ->comment('if set, this port for the socket or url');



            $table->boolean('is_sending_context_to_remote')->default(false)->nullable(false)
                ->comment('if on, then the guids of the relavent attribute,action,element and type are sent to help keep state on the server side');

            $table->boolean('is_caching')->default(true)->nullable(false)
                ->comment('if true then using caching');

            $table->integer('cache_ttl_seconds')->default(null)->nullable()
                ->comment('if set, this is the max time before continuing with attibute read or write');

            $table->jsonb('cache_keys')->default(null)->nullable()
                ->comment('array of string keys to use for the cache comparisons, empty means no comparison');

            $table->integer('total_calls_made')->default(0)->nullable(false)
                ->comment('counts the total number of calls made in the remote lifetime');

            $table->integer('rate_limit_max_per_unit')->default(null)->nullable()
                ->comment('if set, this is the rate limit this remote can be called in the time unit defined in the other column');

            $table->integer('rate_limit_unit_in_seconds')->default(null)->nullable()
                ->comment('if set, this is the size of the time unit for rate limiting');

            $table->dateTime('rate_limit_starts_at')->default(null)->nullable()
                ->comment("if rate limit used, this is how we track the periods starting and ending");

            $table->integer('rate_limit_count')->default(null)->nullable()
                ->comment('shared by all usages of this remote, has usage stats, rolls over per rate limit unit');

            $table->integer('max_concurrent_calls')->default(1)->nullable(false)
                ->comment('number of open calls to this remote at one time');

            $table->string('remote_name',128)->nullable(false)->index()
                ->comment("The unique name of the remote, using the naming rules");



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remotes');
        DB::statement("DROP TYPE type_remote_uri;");
        DB::statement("DROP TYPE type_remote_uri_method;");
        DB::statement("DROP TYPE type_remote_data_format;");
    }
};
