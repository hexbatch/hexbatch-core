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
        Schema::table('elements', function (Blueprint $table) {

            $table->foreignId('element_parent_type_id')
                ->nullable()
                ->default(null)
                ->comment("The type that this element is made from")
                ->index('idx_element_parent_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('element_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the element")
                ->index('idx_element_element_namespace_id')
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->restrictOnDelete();


            //todo remove these pointers and make new table for elements pointing to sets (e|s), one el can point to many sets
            $table->foreignId('pointer_to_child_or_link_set_id')
                ->nullable()->default(null)
                ->comment("each element can point to a set (that is anywhere) as a link")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('pointer_to_parent_set_id')
                ->nullable()->default(null)
                ->comment("each element can also point to a second set, to describe the linkage between them or act as a reference point")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->timestamps();
        });

        DB::statement('ALTER TABLE elements ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE elements ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON elements FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement("CREATE TYPE type_of_set_pointer_mode AS ENUM (
                'link_to_set',
                'parent_child_link'
            );");

        DB::statement("ALTER TABLE elements Add COLUMN set_pointer_mode type_of_set_pointer_mode NOT NULL default 'link_to_set';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER update_modified_time ON elements");

        Schema::table('elements', function (Blueprint $table) {
            $table->dropForeign(['element_parent_type_id']);
            $table->dropForeign(['element_namespace_id']);
            $table->dropForeign(['pointer_to_child_or_link_set_id']);
            $table->dropForeign(['pointer_to_parent_set_id']);

            $table->dropColumn('element_parent_type_id');
            $table->dropColumn('element_namespace_id');
            $table->dropColumn('pointer_to_child_or_link_set_id');
            $table->dropColumn('pointer_to_parent_set_id');

            $table->dropColumn('ref_uuid');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('set_pointer_mode');
        });

        DB::statement("DROP TYPE type_of_set_pointer_mode;");
    }
};

