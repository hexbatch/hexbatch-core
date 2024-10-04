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


            //todo remove the server element_id, this is the public and private elements from the ns home set
            $table->foreignId('server_element_id')
                ->nullable()->default(null)
                ->comment("The element having description and hooks. This made from the server type")
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

            $table->timestamp('status_change_at')->nullable()->default(null)
                ->comment('When the last status was made at');

            $table->string('server_name',30)->unique();

            $table->string('server_domain')->unique()
                ->nullable(false)
                ->comment("the url to the server, example localhost, eggs.waffle_time.org");

            $table->text('server_public_key')
                ->nullable()->default(null)
                ->comment("optional public key used to encrypt the data, instead of token");
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
            $table->dropForeign(['server_element_id']);


            $table->dropColumn('server_type_id');
            $table->dropColumn('owning_namespace_id');
            $table->dropColumn('server_element_id');


            $table->dropColumn('ref_uuid');
            $table->dropColumn('server_domain');
            $table->dropColumn('status_change_at');
            $table->dropColumn('server_status');
            $table->dropColumn('server_name');
            $table->dropColumn('server_public_key');

            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');

        });

        DB::statement("DROP TYPE type_of_server_status;");
    }
};
