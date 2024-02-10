<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
  user: required
        name : unique in urls
        is_retired: default false // if true then cannot be added to element types
        is_on : if off then all read and writes will fail and the remote not called
        timeout_seconds: if an attempt is made to sent to the remote, this is how many seconds until the read or write of the attribute ends in failure

        uri:
            uri_type: (none,url,port,console,manual)
            uri_method (post, get, patch.. etc)
            uri_port:
            uri_string

        read_policy:
            allow: bool
            cache: bool, if true then each last call updates the cache, and if same cache param key values then cache is used
            cache_ttl_seconds: how old the cache is allowed to be
            cache_keys: array of string keys to use for the cache comparisons, empty means no comparison
        write_policy:
            allow: bool,
        data:
            input_attribute_map: array<name of attribute, name of key this goes to server>
            output_map: array<name of server output key or xml path, name of key output object will have>
            remote_data : key value pairs with remote_data_type ('none','basic_auth','bearer_auth','data','header') and name, value and is_secret
        call_schedule:
            call_max_per_unit: x
            call_unit_in_seconds: x
        state
            local_state_init: the initial state for per attribute or action
            element_state_init: the initial shared state for any element that has this an attribute or action with this remote
            type_state_init: the initial shared state for all elements of the same type that has this remote in an attribute or action
            global_state: shared by all usages of this remote


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
    * is_readable
    * is_caching
    * cache_ttl_seconds
    * jsonb cache_keys
    * is_writeable
    * jsonb input_attribute_map
    * jsonb output_map
    call_max_per_unit
    call_unit_in_seconds
    jsonb local_state_init
    jsonb element_state_init
    jsonb type_state_init
    jsonb global_state
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

        DB::statement("CREATE TYPE type_remote_uri AS ENUM (
            'none','url','socket','console','manual'
            );");

        DB::statement("ALTER TABLE remotes Add COLUMN uri_type type_remote_uri  NOT NULL default 'none';");

        DB::statement("CREATE TYPE type_remote_uri_method AS ENUM (
            'none','post','get','put','patch','delete'
            );");

        DB::statement("ALTER TABLE remotes Add COLUMN uri_method_type type_remote_uri_method  NOT NULL default 'none';");

        DB::statement('ALTER TABLE remotes ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE remotes ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remotes FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('remotes', function (Blueprint $table) {
            $table->string('uri_string')->nullable(false)->comment("The url, socket command, console command or hint of the manual call");

            $table->integer('uri_port')->default(null)->nullable()
                ->comment('if set, this port for the socket or url');

            $table->boolean('is_readable')->default(true)->nullable(false)
                ->comment('if false then attached attribute not readable');

            $table->boolean('is_caching')->default(true)->nullable(false)
                ->comment('if true then using caching');

            $table->integer('cache_ttl_seconds')->default(null)->nullable()
                ->comment('if set, this is the max time before continuing with attibute read or write');

            $table->jsonb('cache_keys')->default(null)->nullable()
                ->comment('array of string keys to use for the cache comparisons, empty means no comparison');

            $table->boolean('is_writable')->default(true)->nullable(false)
                ->comment('if false then attached attribute not writable');

            $table->integer('rate_limit_max_per_unit')->default(null)->nullable()
                ->comment('if set, this is the rate limit this remote can be called in the time unit defined in the other column');

            $table->integer('rate_limit_unit_in_seconds')->default(null)->nullable()
                ->comment('if set, this is the size of the time unit for rate limiting');

            $table->dateTime('rate_limit_starts_at')->default(null)->nullable()
                ->comment("if rate limit used, this is how we track the periods starting and ending");

            $table->integer('rate_limit_count')->default(null)->nullable()
                ->comment('shared by all usages of this remote, has usage stats, rolls over per rate limit unit');


            $table->jsonb('local_state_init')->default(null)->nullable()
                ->comment('the initial state for per attribute or action');

            $table->jsonb('element_state_init')->default(null)->nullable()
                ->comment('the initial shared state for any element that has this an attribute or action with this remote');

            $table->jsonb('type_state_init')->default(null)->nullable()
                ->comment('the initial shared state for all elements of the same type that has this remote in an attribute or action');

            $table->jsonb('global_state')->default(null)->nullable()
                ->comment('shared by all usages of this remote');

            $table->jsonb('global_cache')->default(null)->nullable()
                ->comment('shared by all usages of this remote, to store cache data');


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
    }
};
