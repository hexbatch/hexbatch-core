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
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->timestamps();

            //todo add column has_events, default true.
            // this is only set to false if by group or mutual operation
            // If false then no events raised, and no rules fired, entering , leaving set, or inside set.
            // Children sets not affected.
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
        });
    }
};
