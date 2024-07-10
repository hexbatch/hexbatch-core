<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
parent_attribute_id
    user_group_id
    group_type: usage|read|write
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attribute_value_lookups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('value_lookup_attribute_id')
                ->nullable(false)
                ->comment("The attribute this rule is for")
                ->index('idx_value_lookup_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('value_managed_by_attribute_id')
                ->nullable(false)
                ->comment("The attribute that manages this. ".
                    "Set to the ancestor|parent|child based on who set this in the inheritance chain. "
                )
                ->index('idx_value_managed_by_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('value_lookup_value_id')
                ->nullable(false)
                ->comment("The attribute default value this is about")
                ->index('idx_value_lookup_value_id')
                ->constrained('attribute_values')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->timestamps();
        });


        DB::statement("ALTER TABLE attribute_value_lookups ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_value_lookups FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_value_lookups');

    }
};
