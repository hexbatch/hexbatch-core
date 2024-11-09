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

            $table->boolean('is_default_phase') //todo flip the others off if this is set
                ->nullable(false)->default(false)
                ->comment("If true then only that row can be true, and the others set as false");

            $table->timestamps();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");
        });

        DB::statement('ALTER TABLE phases ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE phases ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
                CREATE TRIGGER update_modified_time BEFORE UPDATE ON phases FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
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
