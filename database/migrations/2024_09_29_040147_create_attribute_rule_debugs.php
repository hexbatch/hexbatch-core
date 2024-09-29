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

        Schema::create('attribute_rule_debugs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pending_thing_id')
                ->nullable()->default(null)
                ->comment("Rules can be chained")
                ->index('idx_pending_thing_id')
                ->constrained('attribute_rules')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('debuggee_rule_id')
                ->nullable()->default(null)
                ->comment("Rules can be chained")
                ->index('idx_debuggee_rule_id')
                ->constrained('attribute_rules')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('found_trigger_attribute_id')
                ->nullable()->default(null)
                ->comment("Found this attribute")
                ->unique('idx_debug_found_trigger_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('found_type_associated_id')
                ->nullable()->default(null)
                ->comment("Found this type")
                ->unique('idx_debug_found_type_associated_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('found_data_attribute_id')
                ->nullable()->default(null)
                ->comment("Found this attribute")
                ->unique('idx_found_data_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('found_target_attribute_id')
                ->nullable()->default(null)
                ->comment("Found this attribute")
                ->unique('idx_debug_found_target_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('found_target_element_id')
                ->nullable()->default(null)
                ->comment("Found this element to act on")
                ->unique('idx_debug_found_target_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->timestamps();

            $table->jsonb('data_value')->nullable()->default(null)
                ->comment('found this value from data');
        });

        DB::statement("ALTER TABLE attribute_rule_debugs ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_rule_debugs FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_rule_debugs');
    }
};
