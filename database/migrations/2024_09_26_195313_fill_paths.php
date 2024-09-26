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

            $table->foreignId('path_owner_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the path")
                ->index('idx_path_owner_id')
                ->constrained('user_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('parent_path_id')
                ->nullable()->default(null)
                ->comment("Paths have nested rules")
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('path_type_id')
                ->nullable()->default(null)
                ->comment("The type the path part may be about")
                ->index('idx_path_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('path_attribute_id')
                ->nullable()->default(null)
                ->comment("The attribute the path part may be about")
                ->index('idx_path_attribute_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('path_element_set_id')
                ->nullable()->default(null)
                ->comment("The attribute the path part may be about")
                ->index('idx_path_element_set_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->timestamps();

            $table->integer('path_min_gap')->nullable()->default(null)
                ->comment("The min number of relationships that must exist between here and there");

            $table->integer('path_max_gap')->nullable()->default(null)
                ->comment("How max number of relationships must exist between here and there");
        });

        DB::statement('ALTER TABLE paths ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE paths ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON paths FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement("ALTER TABLE paths Add COLUMN path_logic type_of_child_logic NOT NULL default 'and';");

        DB::statement("CREATE TYPE path_relationship_type AS ENUM (
            'no_relationship',
            'shares_type',
            'in_childish',
            'in_linkish'
            );");

        DB::statement("ALTER TABLE paths Add COLUMN path_relationship path_relationship_type NOT NULL default 'no_relationship';");

        Schema::table('paths', function (Blueprint $table) {


            $table->text('path_attribute_json_path')->nullable()->default(null)
                ->comment("The matching of the attribute value, optional");

            $table->string('path_part_name',128)->nullable(false)->index()
                ->comment("The unique name of the attribute, using the naming rules");
        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_path_parent_name ON paths (parent_path_id,path_part_name) NULLS DISTINCT;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER update_modified_time ON paths");

        Schema::table('paths', function (Blueprint $table) {
            $table->dropForeign(['path_owner_id']);
            $table->dropForeign(['parent_path_id']);
            $table->dropForeign(['path_type_id']);
            $table->dropForeign(['path_attribute_id']);
            $table->dropForeign(['path_element_set_id']);

            $table->dropColumn('path_owner_id');
            $table->dropColumn('parent_path_id');
            $table->dropColumn('path_type_id');
            $table->dropColumn('path_attribute_id');
            $table->dropColumn('path_element_set_id');

            $table->dropColumn('ref_uuid');
            $table->dropColumn('path_min_gap');
            $table->dropColumn('path_max_gap');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('path_logic');
            $table->dropColumn('path_relationship');
            $table->dropColumn('path_attribute_json_path');
            $table->dropColumn('path_part_name');
        });
        DB::statement("DROP TYPE path_relationship_type;");
    }
};
