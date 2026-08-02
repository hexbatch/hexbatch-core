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
        Schema::create('live_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('live_rule_owner_type_id')
                ->nullable(false)
                ->comment("The type which owns this live rule")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('live_rule_trigger_type_id')
                ->nullable(true)
                ->comment("When an element of this type enters this rule is triggered. Null means apply to all elements")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('live_rule_target_type_id')
                ->nullable(false)
                ->comment("The live type this rule is about")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->timestamps();

            $table->boolean('is_passive')->default(false)->nullable(false)
                ->index()
                ->comment('if true, then no permission needed to apply target, but target does not modify element at all, just used in rules and meta');


            $table->boolean('for_child_set_definers')->default(false)->nullable(false)
                ->index()
                ->comment('if true, then this rule is only for child sets created or placed into the set, and not elements. Applied to definer element');


            $table->integer('live_rule_min_triggers')
                ->nullable()->default(null)
                ->comment("Minimum triggers (each element) in set for this rule");

            $table->integer('live_rule_max_triggers')
                ->nullable()->default(null)
                ->comment("Maximum triggers (each element) in set for this rule");

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->uuid('type_owner_uuid')
                ->index()
                ->nullable()->default(null)
                ->comment("used for quick lookup");

            $table->uuid('type_trigger_uuid')
                ->index()
                ->nullable()->default(null)
                ->comment("used for quick lookup");

            $table->uuid('type_target_uuid')
                ->index()
                ->nullable()->default(null)
                ->comment("used for quick lookup");


        });

        DB::statement("CREATE TYPE type_of_live_rule_policy AS ENUM (
                'no_rule',
                'apply_live_on_entry',
                'required_for_entry',
                'blocked_from_entry',
                'disable_if_exists_on_entry',
                'enable_if_exists_on_entry',
                'drop_when_leaving',
                'disable_when_leaving',
                'enable_when_leaving'
                );");

        DB::statement("ALTER TABLE live_rules Add COLUMN live_rule_policy type_of_live_rule_policy NOT NULL default 'no_rule';");

        DB::statement('ALTER TABLE live_rules ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE live_rules ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
                CREATE TRIGGER update_modified_time BEFORE UPDATE ON live_rules FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
            ");


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_rules');
        DB::statement("DROP TYPE type_of_live_rule_policy;");
    }
};
