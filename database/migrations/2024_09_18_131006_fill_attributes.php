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

        Schema::table('attributes', function (Blueprint $table) {

            $table->foreignId('owner_element_type_id')
                ->nullable()
                ->default(null)
                ->comment("The type that owns this attribute")
                ->index('idx_attribute_owner_element_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('parent_attribute_id')
                ->nullable()
                ->default(null)
                ->comment("The optional parent of the attribute")
                ->index('idx_attribute_parent_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->foreignId('pointer_id')
                ->nullable()
                ->default(null)
                ->comment("if the value points to something")
                ->index('idx_attribute_pointer_id')
                ->constrained('attribute_pointers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->boolean('is_retired')->default(false)->nullable(false)
                ->index('idx_is_retired')
                ->comment('if true then cannot be added as parent or added to anything');


            $table->boolean('is_final')->default(false)->nullable(false)
                ->comment('if true then cannot be used as a parent');

            $table->boolean('is_system')->default(false)->nullable(false)
                ->index('idx_attr_is_system')
                ->comment('if true then this attribute is a standard attribute');

            $table->boolean('is_nullable')->default(true)->nullable(false)
                ->comment('if true then value is nullable');


            $table->timestamps();


            $table->text('value_json_path')->nullable()->default(null)
                ->comment("if set the value json has to match this, pointer whitelist can apply");

            $table->jsonb('attribute_value')
                ->nullable()->default(null)->comment("The value of the attribute");

            $table->string('attribute_name',128)->nullable(false)->index()
                ->comment("The unique name of the attribute, using the naming rules");


        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_type_parent_name ON attributes (owner_element_type_id,attribute_name) NULLS NOT DISTINCT;");



        DB::statement('ALTER TABLE attributes ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE attributes ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attributes FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        //for rule look at rule linked in pointer, bounds itself stored in pointer
        DB::statement("CREATE TYPE type_of_attribute AS ENUM (
            'value',
             'rule',
            'meta_author','meta_copywrite','meta_url','meta_rating','meta_icu_language',
            'meta_mime_type','meta_icu_locale','meta_icu_location',
            'read_time_bounds','write_time_bounds',
            'read_map_location_bounds','write_map_location_bounds',
            'read_shape_location_bounds','write_shape_location_bounds'

            );");

        DB::statement("ALTER TABLE attributes Add COLUMN attribute_type type_of_attribute_rule NOT NULL default 'inactive';");
    } //up

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER update_modified_time ON attributes");

        Schema::table('attributes', function (Blueprint $table) {
            $table->dropForeign(['parent_attribute_id']);
            $table->dropForeign(['pointer_id']);

            $table->dropColumn('parent_attribute_id');
            $table->dropColumn('pointer_id');
            $table->dropColumn('ref_uuid');
            $table->dropColumn('is_retired');
            $table->dropColumn('is_final');
            $table->dropColumn('is_system');
            $table->dropColumn('is_nullable');
            $table->dropColumn('value_json_path');
            $table->dropColumn('attribute_value');
            $table->dropColumn('attribute_name');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('attribute_type');
        });

        DB::statement("DROP TYPE type_of_attribute;");


    }
};
