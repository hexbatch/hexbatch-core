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
                ->nullable(false)
                ->comment("When an element of this type enters this rule is triggered")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('live_rule_about_live_type_id')
                ->nullable(false)
                ->comment("The live type this rule is about")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->timestamps();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");


        });

        DB::statement("CREATE TYPE type_of_live_rule_policy AS ENUM (
                'no_rule',
                'apply_live', 'required_for_entry','blocked_from_entry',
                'disable_if_exists_on_entry','enable_if_exists_on_entry',
                'enforce_stack','drop_when_leaving','drop_when_leaving_stack'
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
