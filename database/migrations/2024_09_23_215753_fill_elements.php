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

            $table->foreignId('element_phase_id')
                ->nullable(false)
                ->comment("The phase this belongs to")
                ->index()
                ->constrained('phases')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('element_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the element")
                ->index('idx_element_element_namespace_id')
                ->constrained('user_namespaces')
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
            $table->dropForeign(['element_phase_id']);

            $table->dropColumn('element_parent_type_id');
            $table->dropColumn('element_namespace_id');
            $table->dropColumn('element_phase_id');

            $table->dropColumn('ref_uuid');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });

    }
};

