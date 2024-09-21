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

            $table->foreignId('rule_owner_id')
                ->nullable(false)
                ->comment("The attribute that owns this rule")
                ->index('idx_rule_owner_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('target_attribute_id')
                ->nullable(false)
                ->comment("The attribute this rule is about. This can be a parent or ancestor, affecting all decendants")
                ->index('idx_target_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('rule_weight')->nullable(false)->default(1)
                ->comment("when combined with others of its type, how important is this?");

            $table->integer('rule_value')->nullable(false)->default(1)
                ->comment("summed with others of its type, if total <= 0 rule is not applied");

            $table->text('rule_json_path')->nullable()->default(null)
                ->comment("if set matches path to the the target json value");


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
