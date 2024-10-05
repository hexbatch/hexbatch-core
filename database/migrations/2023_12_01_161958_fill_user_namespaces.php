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
        Schema::table('user_namespaces', function (Blueprint $table) {



            $table->foreignId('namespace_user_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of this type")
                ->index('idx_element_owner_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('namespace_server_id')
                ->nullable()
                ->default(null)
                ->comment("The server this user belongs to, null means this one")
                ->index('idx_namespace_server_id')
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('namespace_type_id')
                ->nullable()->default(null)
                ->comment("The type made for this user. This is derived from at least one server type")
                ->unique('udx_namespace_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            //get the public and private types from the elements
            $table->foreignId('public_element_id')
                ->nullable()->default(null)
                ->comment("The element having the public information for the user")
                ->unique('udx_user_public_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('private_element_id')
                ->nullable()->default(null)
                ->comment("The element having the private information for the user")
                ->unique('udx_private_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('base_namespace_attribute_id')
                ->nullable()->default(null)
                ->comment("This is the attribute that is the parent for all attributes the user makes, which do not have a parent when created")
                ->unique('udx_base_namespace_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->nullOnDelete();



            $table->foreignId('namespace_home_set_id')
                ->nullable()
                ->default(null)
                ->comment("The user element that stores the user data")
                ->unique('udx_user_element_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->restrictOnDelete();





            $table->timestamps();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->text('namespace_public_key')
                ->nullable()->default(null)
                ->comment("optional public key used to encrypt the data, instead of token");

            $table->string('namespace_name',61)
                ->nullable(false)
                ->index()
                ->comment("All things created by this user_token is prefixed by this name");
        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_server_username ON user_namespaces (namespace_server_id,namespace_name) NULLS NOT DISTINCT;");

        DB::statement('ALTER TABLE user_namespaces ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE user_namespaces ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON user_namespaces FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER update_modified_time ON user_namespaces");

        Schema::table('user_namespaces',function(Blueprint $table) {


            $table->dropForeign(['namespace_user_id']);
            $table->dropForeign(['namespace_server_id']);
            $table->dropForeign(['namespace_type_id']);
            $table->dropForeign(['public_element_id']);
            $table->dropForeign(['private_element_id']);
            $table->dropForeign(['base_namespace_attribute_id']);
            $table->dropForeign(['namespace_home_set_id']);

            $table->dropColumn('namespace_user_id');
            $table->dropColumn('namespace_server_id');
            $table->dropColumn('namespace_type_id');
            $table->dropColumn('public_element_id');
            $table->dropColumn('private_element_id');
            $table->dropColumn('base_namespace_attribute_id');
            $table->dropColumn('namespace_home_set_id');

            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('namespace_name');
            $table->dropColumn('ref_uuid');
            $table->dropColumn('namespace_public_key');
        });

    }
};
