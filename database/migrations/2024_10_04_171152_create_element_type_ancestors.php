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
        Schema::create('element_type_ancestors', function (Blueprint $table) {
            $table->id();
            //A , B, Gap where A is every type and B is one row for each entire ancestor chain, and Gap is how many generations

            $table->foreignId('owning_child_type_id')
                ->nullable(false)
                ->comment("The type which has parents and/or ancestors")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('ancestor_type_id')
                ->nullable(false)
                ->comment("The type which is the parent or ancestor")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('type_gap')->nullable(false)
                ->comment('How many generations apart');
        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_owning_ancestor_type ON element_type_ancestors (owning_child_type_id,ancestor_type_id) NULLS NOT DISTINCT;");


        DB::statement("
            CREATE OR REPLACE FUNCTION update_type_ancestors()
                RETURNS TRIGGER AS $$
            BEGIN

                DELETE FROM element_type_ancestors a WHERE a.owning_child_type_id = NEW.child_type_id;



                IF true THEN
                    INSERT INTO element_type_ancestors(owning_child_type_id,ancestor_type_id,type_gap )

                        with recursive type_descendants as
                                           (
                                               (
                                                   select par_a.child_type_id as owning_child_type_id,
                                                          par_a.child_type_id as current_id,
                                                          par_a.parent_type_id as ancestor_type_id,
                                                          1 as type_gap
                                                   from element_type_parents as par_a where par_a.child_type_id = NEW.child_type_id
                                                       AND  par_a.parent_type_approval  IN ('publishing_approved')
                                               )
                                               union all
                                               (
                                                   select type_descendants.owning_child_type_id as owning_child_type_id ,
                                                          par_b.child_type_id as current_id,
                                                          par_b.parent_type_id                  as ancestor_type_id,
                                                          type_gap + 1                          as type_gap


                                                   from element_type_parents as par_b
                                                            inner join type_descendants on type_descendants.ancestor_type_id = par_b.child_type_id
                                                   where par_b.parent_type_approval  IN ('publishing_approved')
                                               )
                                           )

                        select distinct

                                        type_descendants.owning_child_type_id,
                                        type_descendants.ancestor_type_id,
                                        type_descendants.type_gap
                        from  type_descendants

                        order by type_gap

                    ON CONFLICT DO NOTHING;
                END IF;
                RETURN NEW;
            END;
            $$ language 'plpgsql';
        ");




        DB::statement("
            CREATE TRIGGER add_type_ancestors_after_update AFTER UPDATE ON element_type_parents FOR EACH ROW EXECUTE PROCEDURE update_type_ancestors();
        ");

        DB::statement("
            CREATE TRIGGER add_type_ancestors_after_insert AFTER INSERT ON element_type_parents FOR EACH ROW EXECUTE PROCEDURE update_type_ancestors();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_ancestors');
        DB::statement("DROP TRIGGER  IF EXISTS  add_type_ancestors_after_update ON element_type_parents");
        DB::statement("DROP TRIGGER  IF EXISTS  add_type_ancestors_after_insert ON element_type_parents");
        DB::statement("DROP FUNCTION IF EXISTS update_type_ancestors();");

    }
};
