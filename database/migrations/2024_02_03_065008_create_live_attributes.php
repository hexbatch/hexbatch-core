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
        /*
         * Live attributes record which live attributes mask the regular attribute of the type
         * A live attribute can mask something if it has an attribute derived from the earlier attribute.
         * It does not matter much which subtype that exposed attribute on the live is from
         * Identical attributes do not mask each other
         */
        Schema::create('live_attributes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_live_id')
                ->nullable(false)
                ->comment("The live id this is about")
                ->index()
                ->constrained('live_applied')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('earlier_attribute_id')
                ->nullable(false)
                ->comment("The attribute that was used in this element before the live was added")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('later_attribute_id')
                ->nullable(false)
                ->comment("The attribute from the live id")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


        });

        DB::statement("CREATE TYPE type_of_live_attribute_behavior AS ENUM ('normal', 'filter','block');");

        DB::statement("ALTER TABLE live_attributes Add COLUMN live_attribute_behavior type_of_live_attribute_behavior NOT NULL default 'normal';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_attributes');
        DB::statement("DROP TYPE type_of_live_attribute_behavior;");

    }
};
