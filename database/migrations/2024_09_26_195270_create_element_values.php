<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

            $table->foreignId('horde_originating_type_id')
                ->nullable(false)
                ->comment("The type where the attribute came from. This can from be a design child, the type itself, or a live type added onto the element")
                ->index()
                ->constrained('element_types')
                ->restrictOnDelete()
                ->cascadeOnDelete();


            $table->foreignId('horde_attribute_id')
                ->nullable(false)
                ->comment("The attribute that belongs to the type")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->foreignId('element_set_member_id')
                ->nullable()->default(null)
                ->comment("The element/set these values are about")
                ->index()
                ->constrained('element_set_members')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->foreignId('type_set_visibility_id')
                ->nullable()->default(null)
                ->comment("About if the type the attribute belongs to is visible or not in this set")
                ->index()
                ->constrained('element_type_set_visibilities')
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

            $table->boolean('is_on')->default(true)->nullable(false)
                ->comment('if off, then not seen by any rules. This can be local to the set');


            $table->timestamp('value_changed_at')->default(null)->nullable()
                ->comment('Updated when the value is updated here, otherwise null');

            $table->jsonb('element_value')
                ->nullable()->default(null)->comment("The value of the attribute in this row");



            $table->unique(['horde_type_id','horde_originating_type_id','horde_attribute_id']);

        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_values');


    }
};
