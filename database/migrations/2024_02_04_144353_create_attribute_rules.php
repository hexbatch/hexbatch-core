<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 attribute_force_rules:
        parent_attribute_id
        attribute_id:
        weight: integer:  (negative means more repulsed, positive means more attracted)
        numeric_min: number nullable
        numeric_max: number nullable
        string_value: (constant or regex)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attribute_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('target_attribute_id')
                ->nullable(false)
                ->comment("The attribute this rule is about")
                ->index('idx_target_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('rule_weight')->nullable(false)->default(1)
                ->comment("how important is this rule?");

            $table->float('rule_numeric_min')->nullable()->default(null)
                ->comment("if set and target type is number, then this is the min allowed for the value");

            $table->float('rule_numeric_max')->nullable()->default(null)
                ->comment("if set and target type is number, then this is the max allowed for the value");

            $table->string('rule_regex')->nullable()->default(null)
                ->comment("if set and target value type is plain string, then regex filters this");


            $table->timestamps();
        });

        DB::statement("CREATE TYPE type_of_attribute_rule AS ENUM (
            'inactive',
            'allergy' , 'affinity',
            'read','write','required','forbidden'
            );");

        DB::statement("ALTER TABLE attribute_rules Add COLUMN rule_type type_of_attribute_rule NOT NULL default 'inactive';");

        DB::statement("ALTER TABLE attribute_rules ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_rules FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_rules');
        DB::statement("DROP TYPE type_of_attribute_rule;");
    }
};
