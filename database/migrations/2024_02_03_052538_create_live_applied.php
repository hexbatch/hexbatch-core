<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Live types are the only way to filter events, and once the live type if off or muted, then the event is no longer filtered
       Down-set cannot undo a type applied but can apply an earlier type
     *
     */
    public function up(): void
    {
        Schema::create('live_applied', function (Blueprint $table) {
            $table->id();


            $table->foreignId('live_target_element_id')
                ->nullable(false)
                ->comment("The element the live type is applied to")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('live_applied_type_id')
                ->nullable(false)
                ->comment("The type being applied")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('live_applied_in_set_id')
                ->nullable(false)
                ->comment("The set this took place at, down-set will be affected")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


        });


        Schema::table('live_applied', function (Blueprint $table) {
            $table->foreignId('masking_live_id')
                ->nullable()->default(null)
                ->comment("When the same element has another live type applied downset from its orginal application")
                ->index()
                ->constrained('live_applied')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            $table->boolean('is_live_enabled')->default(true)->nullable(false)
                ->index()
                ->comment('if false then data kept, but not accesable and live type does not exist for rules');

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");



            $table->uuid('live_applied_phase_uuid')
                ->index()
                ->nullable()->default(null)
                ->comment("used for quick lookup");

            $table->uuid('live_applied_element_uuid')
                ->index()
                ->nullable()->default(null)
                ->comment("used for quick lookup");

            $table->uuid('live_applied_type_uuid')
                ->index()
                ->nullable()->default(null)
                ->comment("used for quick lookup");

            $table->uuid('live_applied_set_uuid')
                ->index()
                ->nullable()->default(null)
                ->comment("used for quick lookup");

            $table->timestamps();
        });



        DB::statement('ALTER TABLE  live_applied ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE  live_applied ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON  live_applied FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_applied');
    }
};
