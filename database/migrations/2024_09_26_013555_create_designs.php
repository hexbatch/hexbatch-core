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
        Schema::create('designs', function (Blueprint $table) {
            $table->id();



            $table->foreignId('design_attribute_id')
                ->nullable()->default(null)
                ->comment("The attribute that has this type design info")
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('design_type_id')
                ->nullable()->default(null)
                ->comment("The type this design information is for")
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('design_set_id')
                ->nullable()->default(null)
                ->comment("The set this design information is for")
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->timestamps();

            $table->text('design_text_notes')->nullable()->default(null)
                ->comment("Any notes about the attribute");

            $table->jsonb('design_look')
                ->nullable()->default(null)->comment("For coloring and shading the shape");
        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_design_type_id ON designs (design_type_id) NULLS NOT DISTINCT;");

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_design_attribute_id ON designs (design_attribute_id) NULLS NOT DISTINCT;");

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_design_set_id ON designs (design_set_id) NULLS NOT DISTINCT;");

        DB::statement("ALTER TABLE designs ADD CONSTRAINT chk_one_design_target_is_not_null CHECK (
            num_nonnulls(design_type_id, design_attribute_id,design_set_id) = 1)
        ;");

        DB::statement("ALTER TABLE designs
                              Add COLUMN design_geom
                              geometry
                              ;
                    ");

        DB::statement('ALTER TABLE designs ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE designs ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON designs FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designs');
    }
};
