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

            $table->foreignId('found_trigger_element_id')
                ->nullable()->default(null)
                ->comment("Found trigger")
                ->unique('idx_debug_found_trigger_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('found_data_element_id')
                ->nullable()->default(null)
                ->comment("Found data")
                ->unique('idx_debug_found_data_element_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('found_target_element_id')
                ->nullable()->default(null)
                ->comment("Found target")
                ->unique('idx_debug_found_target_element_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->timestamps();

            $table->jsonb('read_data_value')->nullable()->default(null)
                ->comment('read this');

            $table->jsonb('write_data_value')->nullable()->default(null)
                ->comment('wrote this');
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
