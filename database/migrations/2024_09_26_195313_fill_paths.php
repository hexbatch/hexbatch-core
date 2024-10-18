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
        Schema::table('paths', function (Blueprint $table) {

            $table->foreignId('path_owning_namespace_id')
                ->nullable(false)
                ->comment("The owner of the path")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('path_handle_element_id')
                ->nullable()
                ->comment("Optional element for repeated use of the path")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->text('path_compiled_sql')
                ->nullable()->default(null)
                ->comment("Stores sql this path was converted to, the parents will include the children");

            $table->string('path_name',128)->nullable(false)
                ->comment("the name of the path, its unique to the namespace");

        });
        DB::statement('ALTER TABLE paths ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE paths ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON paths FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");


        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_path_name_on_namespace ON paths (path_owning_namespace_id,path_name) NULLS NOT DISTINCT;");

        DB::statement("CREATE TYPE type_of_path_status AS ENUM ('design', 'ready','error','sabotaged');");

        DB::statement("ALTER TABLE paths Add COLUMN path_status type_of_path_status NOT NULL default 'design';");



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER update_modified_time ON paths");

        Schema::table('paths', function (Blueprint $table) {
            $table->dropForeign(['path_owning_namespace_id']);
            $table->dropForeign(['path_handle_element_id']);

            $table->dropColumn('path_owning_namespace_id');
            $table->dropColumn('path_handle_element_id');


            $table->dropColumn('ref_uuid');

            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('path_name');
            $table->dropColumn('path_compiled_sql');
            $table->dropColumn('path_status');
        });
        DB::statement("DROP TYPE type_of_path_status;");

    }
};
