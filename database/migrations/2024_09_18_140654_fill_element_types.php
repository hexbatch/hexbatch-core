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

            $table->foreignId('user_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the remote")
                ->index('idx_element_type_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('editing_user_group_id')
                ->nullable()->default(null)
                ->comment("Optional user group that allows editing of type. Null fallsback on the owner admin group")
                ->index('idx_element_type_editing_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('inheriting_user_group_id')
                ->nullable()->default(null)
                ->comment("Optional user group that controls inheritance, and who can use this as ancestor. Null is anyone")
                ->index('idx_element_type_inheriting_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('new_elements_user_group_id')
                ->nullable()->default(null)
                ->comment("Additional users who can create elements from this token. Null is anyone")
                ->index('idx_allowed_creators_group_id')
                ->constrained('user_groups')
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

            $table->string('type_name',128)->nullable(false)->index()
                ->comment("The unique name of the type, using the naming rules");


        });

        DB::statement('ALTER TABLE element_types ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE element_types ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_types FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_user_per_type_name ON element_types (user_id,type_name) NULLS NOT DISTINCT;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER update_modified_time ON element_types");

        Schema::table('element_types', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['editing_user_group_id']);
            $table->dropForeign(['inheriting_user_group_id']);
            $table->dropForeign(['new_elements_user_group_id']);

            $table->dropColumn('user_id');
            $table->dropColumn('editing_user_group_id');
            $table->dropColumn('inheriting_user_group_id');
            $table->dropColumn('new_elements_user_group_id');
            $table->dropColumn('type_name');
            $table->dropColumn('ref_uuid');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('is_retired');
            $table->dropColumn('is_system');
            $table->dropColumn('is_final');
        });
    }
};
