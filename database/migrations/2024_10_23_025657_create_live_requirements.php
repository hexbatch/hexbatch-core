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
        Schema::create('live_requirements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('live_requirement_owner_type_id')
                ->nullable(false)
                ->comment("The type which owns this live requirement")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('live_requirement_needed_type_id')
                ->nullable(false)
                ->comment("The type that is needed ")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique(['live_requirement_owner_type_id','live_requirement_needed_type_id']);

            $table->timestamps();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

        });

        DB::statement('ALTER TABLE live_requirements ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE live_requirements ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
                CREATE TRIGGER update_modified_time BEFORE UPDATE ON live_requirements FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
            ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_requirements');
    }
};
