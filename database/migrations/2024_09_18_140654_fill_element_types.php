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
        Schema::table('element_types', function (Blueprint $table) {

            $table->foreignId('owner_user_type_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the type")
                ->index('idx_type_owner_user_type_id')
                ->constrained('user_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            //todo add table to list which types were received from which servers (element_type_id, server_id, timestamps)
            //todo need table to list which remote types have rules for events, (above table , attribute_event_id)

            $table->foreignId('editing_user_group_id')
                ->nullable()->default(null)
                ->comment("Optional group that allows editing of type. Null fallsback on the owner admin group")
                ->index('idx_element_type_editing_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('inheriting_user_group_id')
                ->nullable()->default(null)
                ->comment("Optional group that controls inheritance, and who can use this as ancestor. Null is anyone")
                ->index('idx_element_type_inheriting_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('new_elements_user_group_id')
                ->nullable()->default(null)
                ->comment("Optinal group who can create elements from this token. Null is anyone")
                ->index('idx_type_new_elements_user_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('type_read_user_group_id')
                ->nullable()->default(null)
                ->comment("Optional whitelist to be able to read any attributes in this type. If missing anyone")
                ->index('idx_type_read_user_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('type_write_user_group_id')
                ->nullable()->default(null)
                ->comment("Optional whitelist to be able to write any attributes in this type. If missing only editor group")
                ->index('idx_type_write_user_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('type_time_bound_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a time bounds.")
                ->index('idx_type_time_bound_id')
                ->constrained('time_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('type_location_map_bound_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a location map bounds")
                ->index('idx_type_location_map_bound_id')
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('type_bound_path_id')
                ->nullable()
                ->default(null)
                ->comment("Only can join sets found in path. Is not evicted due to path result change")
                ->index('idx_type_bound_path_id')
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('type_description_element_id')
                ->nullable()
                ->default(null)
                ->comment("Only can join sets found in path. Is not evicted due to path result change")
                ->unique('udx_type_description_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->timestamps();

            $table->boolean('is_retired')->default(false)->nullable(false)
                ->comment('if true then cannot be added as parent, or have new elements created');

            $table->boolean('is_system')->default(false)->nullable(false)
                ->index('idx_type_is_system')
                ->comment('if true then this attribute is a standard attribute');

            $table->boolean('is_final')->default(false)->nullable(false)
                ->comment('if true then cannot be added as parent');



        });

        DB::statement("ALTER TABLE element_types
                              Add COLUMN type_sum_geom_map
                              geometry
                              ;
                    ");


        Schema::table('element_types', function (Blueprint $table) {
            $table->string('type_name',128)->nullable(false)->index()
                ->comment("The unique name of the type, using the naming rules");
        });

        DB::statement('ALTER TABLE element_types ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE element_types ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_types FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_user_per_type_name ON element_types (owner_user_type_id,type_name) NULLS NOT DISTINCT;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER update_modified_time ON element_types");

        Schema::table('element_types', function (Blueprint $table) {
            $table->dropForeign(['owner_user_type_id']);
            $table->dropForeign(['editing_user_group_id']);
            $table->dropForeign(['inheriting_user_group_id']);
            $table->dropForeign(['new_elements_user_group_id']);
            $table->dropForeign(['type_write_user_group_id']);
            $table->dropForeign(['type_read_user_group_id']);
            $table->dropForeign(['type_time_bound_id']);
            $table->dropForeign(['type_location_map_bound_id']);
            $table->dropForeign(['type_bound_path_id']);
            $table->dropForeign(['type_description_element_id']);

            $table->dropColumn('owner_user_type_id');
            $table->dropColumn('editing_user_group_id');
            $table->dropColumn('inheriting_user_group_id');
            $table->dropColumn('new_elements_user_group_id');
            $table->dropColumn('type_write_user_group_id');
            $table->dropColumn('type_read_user_group_id');
            $table->dropColumn('type_time_bound_id');
            $table->dropColumn('type_location_map_bound_id');
            $table->dropColumn('type_bound_path_id');
            $table->dropColumn('type_description_element_id');

            $table->dropColumn('type_name');
            $table->dropColumn('ref_uuid');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('is_retired');
            $table->dropColumn('is_system');
            $table->dropColumn('is_final');
            $table->dropColumn('type_sum_geom_map');



        });
    }
};
