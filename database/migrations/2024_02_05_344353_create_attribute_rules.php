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

            $table->foreignId('rule_bundle_owner_id')
                ->nullable(false)
                ->comment("The bundle that owns this rule")
                ->index('idx_rule_bundle_owner_id')
                ->constrained('attribute_rule_bundles')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('target_attribute_id')
                ->nullable()->default(null)
                ->comment("If this rule is depending on an attribute,this can be a stand alone, or ancestor, affecting all decendants")
                ->index('idx_target_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->boolean('is_target_including_descendants')->default(true)->nullable(false)
                ->comment('if true then value is nullable');


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
            'required',
            'set_membership_affinity'
            'set_toggle_affinity'
            );");

        // Affinity membership depends on elements in a set to decide to join it when asked by command,
        // Affinity toggle can turn an attribute to not be readable or writable (both at the same time) in a set based on the contents
        // and the required is build time, so no checking there

        DB::statement("ALTER TABLE attribute_rules Add COLUMN rule_type type_of_attribute_rule NOT NULL default 'inactive';");

        DB::statement("ALTER TABLE attribute_rules ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_rules FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('attribute_rules', function (Blueprint $table) {
            $table->string('rule_name',128)->nullable(false)->index()
                ->comment("The unique name of the rule in the bundle, using the naming rules");
        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_rule_parent_name ON attribute_rules (rule_bundle_owner_id,rule_name) NULLS NOT DISTINCT;");

        DB::statement('ALTER TABLE attribute_rules ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');
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
