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
        Schema::create('attribute_shape_event_blockers', function (Blueprint $table) {
            $table->id();


            $table->foreignId('blocking_attribute_id')
                ->nullable(false)
                ->comment("Attribute that will block this event to the other, if they intersect their shapes")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('blocked_attribute_id')
                ->nullable(false)
                ->comment("Attribute that will have the event be blocked if they intersect their shapes")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('block_logic_attribute_id')
                ->nullable()->default(null)
                ->comment("Attribute whose intersection with the others determines what A and B are in the logic")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('blocking_event_id')
                ->nullable()
                ->comment("The set scoped event that will be blocked, null for all")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->timestamps();

        });

        DB::statement("ALTER TABLE attribute_shape_event_blockers Add COLUMN blocking_logic type_of_logic NOT NULL default 'and';");
        DB::statement("ALTER TABLE attribute_shape_event_blockers ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_shape_event_blockers FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('attribute_shape_event_blockers');
    }
};
