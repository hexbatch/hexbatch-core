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

            $table->foreignId('remote_element_type_id')
                ->nullable()
                ->default(null)
                ->comment("This remote type inherits from the standard remote type and the user type of the creator")
                ->unique('udx_remote_element_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            $table->foreignId('remote_element_id')
                ->nullable()
                ->default(null)
                ->comment("The remote element from the type")
                ->unique('udx_remote_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->boolean('is_retired')->default(false)->nullable(false)
                ->comment('if true then cannot be added to attributes or actions');

            $table->boolean('is_on')->default(false)->nullable(false)
                ->comment('if false then cannot call remote');

            $table->integer('uri_port')->default(null)->nullable()
                ->comment('if set, this port for the socket or url');

            $table->integer('total_calls_made')->default(0)->nullable(false)
                ->comment('counts the total number of calls made in the remote lifetime');

            $table->integer('total_errors')->default(0)->nullable(false)
                ->comment('counts the total number of calls made in the remote lifetime');

            $table->timestamps();
        });

        #------------------------------
        DB::statement("CREATE TYPE type_remote_data_format AS ENUM (
            'text','xml','json','yaml'
            );");

        DB::statement("ALTER TABLE remotes Add COLUMN to_remote_format type_remote_data_format  NOT NULL default 'text';");
        DB::statement("ALTER TABLE remotes Add COLUMN from_remote_format type_remote_data_format  NOT NULL default 'text';");


        #------------------------------
        DB::statement("CREATE TYPE type_remote_uri AS ENUM (
            'none','url','console','manual_owner','manual_element','code'
            );");

        DB::statement("ALTER TABLE remotes Add COLUMN uri_type type_remote_uri  NOT NULL default 'none';");
        #------------------------------

        DB::statement("CREATE TYPE type_remote_uri_method AS ENUM (
            'none','post','get','put','patch','delete'
            );");

        DB::statement("ALTER TABLE remotes Add COLUMN uri_method_type type_remote_uri_method  NOT NULL default 'none';");


        //#-------------------------------------
        DB::statement("CREATE TYPE type_remote_uri_protocol AS ENUM (
            'none','http','https'
            );");

        DB::statement("ALTER TABLE remotes Add COLUMN uri_protocol type_remote_uri_protocol  NOT NULL default 'none';");





        DB::statement('ALTER TABLE remotes ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE remotes ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remotes FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('remotes', function (Blueprint $table) {



            $table->boolean('is_caching')->default(true)->nullable(false)
                ->comment('if true then using caching');

            $table->boolean('is_using_cache_on_failure')->default(false)->nullable(false)
                ->comment('if true then if remote fails, and has cache, use cache and it does not fail');

            $table->integer('cache_ttl_seconds')->default(null)->nullable()
                ->comment('if set, this is how long the cache will last');


            $table->integer('rate_limit_max_per_unit')->default(null)->nullable()
                ->comment('if set, this is the rate limit this remote can be called in the time unit defined in the other column');

            $table->integer('rate_limit_unit_in_seconds')->default(null)->nullable()
                ->comment('if set, this is the size of the time unit for rate limiting');

            $table->dateTime('rate_limit_starts_at')->default(null)->nullable()
                ->comment("if rate limit used, this is how we track the periods starting and ending");

            $table->integer('rate_limit_count')->default(null)->nullable()
                ->comment('incremented each time this uri is called, rolls over per rate limit unit');

            $table->integer('max_concurrent_calls')->default(1)->nullable(false)
                ->comment('number of open calls to this uri at one time');

            $table->jsonb('cache_keys')->default(null)->nullable()
                ->comment('array of string keys to use for the cache comparisons, empty means no comparison');

            $table->string('remote_name')->nullable(false)
                ->comment("to id this to humans no checking and can be anything");

            $table->unique(['user_id','remote_name']);


            $table->string('remote_uri_main')->nullable(false)
                ->comment("for url this is domain and subdomain, or ip, for commands and sockets this is the first part before any whitespace. Manual leaves this blank. Code has the class with namespace");

            $table->string('remote_uri_path')->nullable()->default(null)
                ->comment("for url this is the path, for command and port this is what is left over from the field above, this can have placeholders");



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
        DB::statement("DROP TYPE type_remote_uri_protocol;");
    }
};
