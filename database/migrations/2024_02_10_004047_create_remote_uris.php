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
        Schema::create('remote_uris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remote_id')
                ->nullable(false)
                ->comment("The remote this header/key is for")
                ->index('idx_uri_has_remote_id')
                ->constrained('remotes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_sending_context_to_remote')->default(false)->nullable(false)
                ->comment('if on, then the guids of the relavent attribute,action,element and type are sent to help keep state on the server side');


            $table->integer('uri_port')->default(null)->nullable()
                ->comment('if set, this port for the socket or url');

            $table->integer('total_calls_made')->default(0)->nullable(false)
                ->comment('counts the total number of calls made in the remote lifetime');

            $table->integer('total_errors')->default(0)->nullable(false)
                ->comment('counts the total number of calls made in the remote lifetime');

            $table->timestamps();
        });



        DB::statement("ALTER TABLE remote_uris ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remote_uris FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        #------------------------------
        DB::statement("CREATE TYPE type_remote_data_format AS ENUM (
            'none','plain_text','xml','json','yaml'
            );");

        DB::statement("ALTER TABLE remote_uris Add COLUMN uri_to_remote_format type_remote_data_format  NOT NULL default 'none';");
        DB::statement("ALTER TABLE remote_uris Add COLUMN uri_from_remote_format type_remote_data_format  NOT NULL default 'none';");


        #------------------------------
        DB::statement("CREATE TYPE type_remote_uri AS ENUM (
            'none','url','socket','console','manual'
            );");

        DB::statement("ALTER TABLE remote_uris Add COLUMN uri_type type_remote_uri  NOT NULL default 'none';");
        #------------------------------

        DB::statement("CREATE TYPE type_remote_uri_method AS ENUM (
            'none','post','get','put','patch','delete'
            );");

        DB::statement("ALTER TABLE remote_uris Add COLUMN uri_method_type type_remote_uri_method  NOT NULL default 'none';");

        //#-------------------------------------
        DB::statement("CREATE TYPE type_role_uri AS ENUM (
            'default','api_success','api_fail'
            );"); //todo add read and write, keep default but make constraint in here that the same remote cannot have default and (read and/or write) set together
                   //todo in same way, put in api_default so one callback for all can be declared, do not allow api_default to be set if either api_success and api_fail are set

        DB::statement("ALTER TABLE remote_uris Add COLUMN uri_role type_role_uri  NOT NULL default 'default';");


        Schema::table('remote_uris', function (Blueprint $table) {
            $table->unique(['remote_id','uri_role']);
            //todo remote_uri_name (to id this to humans no checking and can be anything)
            //todo remote_uri_description (text anything no checking and can be anything)
            //todo remote_uri_protocol (enum with supported, right now 'none','http','https') only urls fill this out
            //todo remote_uri_main (for url this is the subdomain(s) and domain no path, or ip) , for commands and sockets this is the first part before any whitespace. Manual leaves this blank
            //todo remote_uri_path (for url this is the path, for command and port this is what is left over from the field above) query strings in url are put in the maps as constants. Manual leaves this blank
            $table->string('uri_string')->nullable(false) //todo remove this column
                ->comment("The url, socket command, console command or hint of the manual call");


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_uris');

        DB::statement("DROP TYPE type_role_uri;");
        DB::statement("DROP TYPE type_remote_uri;");
        DB::statement("DROP TYPE type_remote_uri_method;");
        DB::statement("DROP TYPE type_remote_data_format;");
    }
};
