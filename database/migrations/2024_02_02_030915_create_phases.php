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
        Schema::create('phases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('phase_type_id')
                ->nullable() //needs to be nullable so that types can be made first when sys boots
                ->comment("The type which owns this live requirement")
                ->unique()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('edited_by_phase_id')
                ->nullable()->default(null)
                ->comment("The phase that can edit this")
                ->index()
                ->constrained('phases')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_default_phase')
                ->nullable(false)->default(false)
                ->index()
                ->comment("If true then only that row can be true, and the others set as false");

            $table->boolean('is_system')->default(false)->nullable(false)
                ->index()
                ->comment('if true then this phase is a system resource');

            $table->timestamps();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->string('phase_name',128)->nullable(false)->unique()
                ->comment("The unique name of the phase, using the naming rules");
        });

        DB::statement('ALTER TABLE phases ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE phases ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
                CREATE TRIGGER update_modified_time BEFORE UPDATE ON phases FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
            ");

        DB::statement("
               CREATE UNIQUE INDEX only_one_row_with_default_true_uix ON phases (is_default_phase) WHERE (is_default_phase);
            ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phases');
    }
};
