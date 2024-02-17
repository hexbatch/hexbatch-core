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

        uri:
            uri_type: (none,url,port,console,manual)
            uri_method (post, get, patch )
            uri_port
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


 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('remotes', function (Blueprint $table) {


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

            $table->boolean('is_on')->default(true)->nullable(false)
                ->comment('if false then cannot call remote');

            $table->timestamps();
        });

        #------------------------------



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
                ->comment('if set, this is the max time before continuing with attibute read or write');

            $table->jsonb('cache_keys')->default(null)->nullable()
                ->comment('array of string keys to use for the cache comparisons, empty means no comparison');



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
        Schema::table('remotes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['usage_group_id']);
            $table->dropForeign(['remote_element_type_id']);
            $table->dropForeign(['remote_element_id']);

            DB::statement("DROP TRIGGER update_modified_time ON remotes");

            $table->dropColumn(['user_id','usage_group_id','remote_element_type_id','remote_element_id']);
            $table->dropColumn(['ref_uuid','is_retired','is_on','created_at','updated_at']);
            $table->dropColumn(['is_caching','is_using_cache_on_failure','cache_ttl_seconds']);
            $table->dropColumn(['cache_keys','rate_limit_max_per_unit']);
            $table->dropColumn(['rate_limit_unit_in_seconds','rate_limit_starts_at']);
            $table->dropColumn(['rate_limit_count','max_concurrent_calls','remote_name']);
        });



    }
};
