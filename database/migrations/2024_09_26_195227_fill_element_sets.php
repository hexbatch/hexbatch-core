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
        Schema::table('element_sets', function (Blueprint $table) {


            $table->foreignId('parent_set_element_id')
                ->nullable()->default(null)
                ->comment("The element which controls this set")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->timestamps();

            $table->boolean('has_events')
                ->nullable(false)->default(true)
                ->comment("If false then no events raised, and no rules fired entering, leaving set, or inside. Children sets not affected");

            $table->boolean('is_system')->default(false)->nullable(false)
                ->index()
                ->comment('if true then this set is from system boot');

        });

        DB::statement('ALTER TABLE element_sets ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE element_sets ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_sets FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER update_modified_time ON element_sets");

        Schema::table('element_sets', function (Blueprint $table) {
            $table->dropForeign(['parent_set_element_id']);

            $table->dropColumn('parent_set_element_id');

            $table->dropColumn('ref_uuid');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('has_events');
            $table->dropColumn('is_system');
        });
    }
};
