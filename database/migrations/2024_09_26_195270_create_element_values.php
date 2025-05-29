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
        Schema::create('element_values', function (Blueprint $table) {
            $table->id();


            $table->foreignId('horde_type_id')
                ->nullable(false)
                ->comment("The type this is all about")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('horde_live_attributes_id')
                ->nullable(false)
                ->comment("From a live type added onto the element")
                ->index()
                ->constrained('live_attributes')
                ->restrictOnDelete()
                ->cascadeOnDelete();



            $table->foreignId('horde_attribute_id')
                ->nullable(false)
                ->comment("The attribute that belongs to the type")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->foreignId('horde_element_id')
                ->nullable()->default(null)
                ->comment("The value policy per element, the element these values are about")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('horde_set_id')
                ->nullable()->default(null)
                ->comment("The value policy per set, the set these values are about")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('horde_set_member_id')
                ->nullable()->default(null)
                ->comment("When value policy is allowing child sets, the element per set these values are about")
                ->index()
                ->constrained('element_set_members')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();





            $table->foreignId('parent_element_value_id')
                ->nullable()->default(null)
                ->comment("when in push pop context, this is where the last value is")
                ->index()
                ->constrained('element_values')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();



            $table->jsonb('element_value')
                ->nullable()->default(null)->comment("The value of the attribute in this row");



        });

        //nulls need to be included in the unique condition here to set static (not belonging to a set) values
        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_type_org_attr_member ON element_values
            (horde_type_id,horde_attribute_id,horde_element_id,horde_set_id,horde_set_member_id) NULLS NOT DISTINCT;");


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_values');


    }
};
