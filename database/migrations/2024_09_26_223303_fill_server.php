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
        Schema::table('servers', function (Blueprint $table) {

            $table->foreignId('owning_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("The admin namespace for this server")
                ->index('idx_server_owning_namespace_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('server_type_id')
                ->nullable()
                ->default(null)
                ->comment("The link to the server's type")
                ->index('idx_server_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();


            $table->boolean('is_system')->default(false)->nullable(false)
                ->index()
                ->comment('if true then this server is from system boot');

            $table->timestamps();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

        });

        DB::statement("CREATE TYPE type_of_server_status AS ENUM (
            'unknown_server',
            'pending_server',
            'allowed_server',
            'paused_server',
            'blocked_server'
            );");

        DB::statement("ALTER TABLE servers Add COLUMN server_status type_of_server_status NOT NULL default 'unknown_server';");

        Schema::table('servers', function (Blueprint $table) {

            $table->timestamp('status_change_at')->nullable()->default(null)
                ->comment('When the last status was made at'); //todo add trigger to set this when status changes

            $table->timestamp('access_token_expires_at')->nullable()->default(null)
                ->comment('When the access token expires, and needs to be renewed');

            $table->string('server_name',30)->unique();

            $table->string('server_domain')->unique()
                ->nullable(false)
                ->comment("the url to the server, example localhost, eggs.waffle_time.org");


            $table->string('server_access_token')
                ->nullable()->default(null)
                ->comment("the access token to the server, to log in as that user, encrypted");

        });

        DB::statement('ALTER TABLE servers ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE servers ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON servers FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER update_modified_time ON servers");

        Schema::table('servers', function (Blueprint $table) {
            $table->dropForeign(['server_type_id']);
            $table->dropForeign(['owning_namespace_id']);


            $table->dropColumn('server_type_id');
            $table->dropColumn('owning_namespace_id');


            $table->dropColumn('ref_uuid');
            $table->dropColumn('server_domain');
            $table->dropColumn('status_change_at');
            $table->dropColumn('access_token_expires_at');
            $table->dropColumn('server_access_token');
            $table->dropColumn('server_status');
            $table->dropColumn('server_name');
            $table->dropColumn('is_system');

            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');

        });

        DB::statement("DROP TYPE type_of_server_status;");
    }
};
