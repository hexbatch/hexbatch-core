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

            $table->foreignId('server_type_id')
                ->nullable()
                ->default(null)
                ->comment("The link to the server's user type. The user type inherits from the server type")
                ->index('idx_server_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();


            $table->foreignId('server_admin_user_type_id')
                ->nullable()
                ->default(null)
                ->comment("Who changed the last status manually")
                ->index('idx_server_admin_user_type_id')
                ->constrained('user_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('server_element_id')
                ->nullable()->default(null)
                ->comment("The element having description and hooks. Marks this as saved")
                ->unique('udx_server_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->nullOnDelete();



            $table->timestamps();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

        });

        DB::statement("CREATE TYPE type_of_server_status AS ENUM (
            'pending',
            'allowed',
            'paused',
            'blocked'
            );");

        DB::statement("ALTER TABLE servers Add COLUMN server_status type_of_server_status NOT NULL default 'pending';");

        Schema::table('servers', function (Blueprint $table) {

            $table->dateTime('status_change_at')->nullable()->default(null)
                ->comment('When the last status was made at');

            $table->string('server_domain')->unique()
                ->nullable(false)
                ->comment("the url to the server");

            $table->string('server_incoming_token')
                ->nullable()->default(null)
                ->comment("the token to send the server for any requests");

            $table->string('server_outgoing_token')->unique()
                ->nullable()->default(null)
                ->comment("The token sent by the server to here");
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
            $table->dropForeign(['server_admin_user_type_id']);
            $table->dropForeign(['server_element_id']);


            $table->dropColumn('server_type_id');
            $table->dropColumn('server_admin_user_type_id');
            $table->dropColumn('server_element_id');


            $table->dropColumn('ref_uuid');
            $table->dropColumn('server_domain');
            $table->dropColumn('status_change_at');
            $table->dropColumn('server_status');
            $table->dropColumn('server_incoming_token');
            $table->dropColumn('server_outgoing_token');

            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');

        });

        DB::statement("DROP TYPE type_of_server_status;");
    }
};
