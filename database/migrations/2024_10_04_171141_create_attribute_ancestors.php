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
        Schema::create('attribute_ancestors', function (Blueprint $table) {
            $table->id();
            // A , B, Gap where A is every attribute and B is one row for each entire ancestor chain, and Gap is how many generations

            $table->foreignId('child_attribute_id')
                ->nullable(false)
                ->comment("The attribute which has parents and/or ancestors")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('ancestor_attribute_id')
                ->nullable(false)
                ->comment("The attribute which is the parent or ancestor")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('attribute_gap')->nullable(false)
                ->comment('How many generations apart');
        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_owning_ancestor_attribute ON attribute_ancestors (child_attribute_id,ancestor_attribute_id) NULLS NOT DISTINCT;");

        DB::statement("
            CREATE OR REPLACE FUNCTION update_attribute_ancestors()
                RETURNS TRIGGER AS $$
            BEGIN

                DELETE FROM attribute_ancestors a WHERE a.child_attribute_id = NEW.id;

                IF NEW.parent_attribute_id IS NOT NULL AND NEW.attribute_approval = 'publishing_approved' THEN
                    INSERT INTO attribute_ancestors(child_attribute_id,ancestor_attribute_id,attribute_gap )

                        with recursive attr_descendants as
                           (
                               (
                                   select attr_a.id as child_attribute_id,
                                          attr_a.parent_attribute_id as ancestor_attribute_id,
                                          1 as attribute_gap
                                   from attributes as attr_a where attr_a.id = NEW.id
                                   and attr_a.attribute_approval = 'publishing_approved'

                               )
                               union all
                               (
                                   select attr_descendants.child_attribute_id as child_attribute_id ,
                                          attr_b.parent_attribute_id as ancestor_attribute_id,
                                          attribute_gap + 1 as attribute_gap


                                   from attributes as attr_b
                                            inner join attr_descendants on attr_descendants.ancestor_attribute_id = attr_b.id
                                   where attr_b.parent_attribute_id is NOT NULL
                                   and attr_b.attribute_approval = 'publishing_approved'
                               )
                           )

                        select distinct attr_descendants.child_attribute_id,
                                        attr_descendants.ancestor_attribute_id,
                                        attr_descendants.attribute_gap
                        from  attr_descendants

                        order by attribute_gap

                    ON CONFLICT DO NOTHING;
                  END IF;
                RETURN NEW;
            END;
            $$ language 'plpgsql';
        ");




        DB::statement("
            CREATE TRIGGER add_ancestors_after_update AFTER UPDATE ON attributes FOR EACH ROW EXECUTE PROCEDURE update_attribute_ancestors();
        ");

        DB::statement("
            CREATE TRIGGER add_ancestors_after_insert AFTER INSERT ON attributes FOR EACH ROW EXECUTE PROCEDURE update_attribute_ancestors();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_ancestors');

        DB::statement("DROP TRIGGER  IF EXISTS  add_ancestors_after_update ON attributes");
        DB::statement("DROP TRIGGER  IF EXISTS  add_ancestors_after_insert ON attributes");
        DB::statement("DROP FUNCTION IF EXISTS update_attribute_ancestors();");
        DB::statement("DROP FUNCTION IF EXISTS insert_attribute_ancestors();");
    }
};
