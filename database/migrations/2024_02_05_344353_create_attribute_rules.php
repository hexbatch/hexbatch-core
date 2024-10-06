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

        Schema::create('attribute_rules', function (Blueprint $table) {
            $table->id();


            $table->foreignId('owning_attribute_id')
                ->nullable()
                ->default(null)
                ->comment("The parent of the top level of the rule chain. Each attribute can have one rule chain")
                ->unique()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('rule_handle_element_id')
                ->nullable()
                ->comment("Optional element for helping describe the rule tree")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->restrictOnDelete();



            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");




            $table->timestamps();

            $table->string('rule_name',256)
                ->nullable(false)->index()
                ->comment("The name of the rule");
        });




        DB::statement("ALTER TABLE attribute_rules ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_rules FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('attribute_rules', function (Blueprint $table) {

            $table->string('filter_json_path')
                ->nullable()->default(null)
                ->comment("if set then the data this rule node has will filter the child results to only have matching in one json to give to the parent");


        });


        DB::statement('ALTER TABLE attribute_rules ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_rules');
    }
};
