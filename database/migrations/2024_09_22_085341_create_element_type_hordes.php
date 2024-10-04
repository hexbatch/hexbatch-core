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
        Schema::create('element_type_hordes', function (Blueprint $table) {
            $table->id();


            $table->foreignId('horde_type_id')
                ->nullable()->default(null)
                ->comment("The type that holds the attributes, which are selected when the type is created from the parents")
                ->index('idx_horde_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('horde_attribute_id')
                ->nullable()->default(null)
                ->comment("The attribute that belongs to the type")
                ->index('idx_horde_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique(['horde_type_id','horde_attribute_id']);

            //todo rm
            $table->boolean('is_whitelisted_reading')->default(false)->nullable(false)
                ->comment('if true then check the whitelist before reading');

            //todo rm
            $table->boolean('is_whitelisted_writing')->default(false)->nullable(false)
                ->comment('if true then check the whitelist before writing');


            //todo rm
            $table->boolean('is_map_bound')->default(false)->nullable(false)
                ->comment('if true then this attribute will not readable/writable in all sets, check the attribute location bounds');

            //todo rm
            $table->boolean('is_shape_bound')->default(false)->nullable(false)
                ->comment('if true then this attribute will not be readable/writable in all shapes, check the attribute location bounds');

            $table->boolean('is_time_bound')->default(false)->nullable(false)
                ->comment('if true then this attribute will not be readable/writable every time, check the attribute time bounds');

            //todo change to enum set_value_behavior
            $table->boolean('is_per_set_value')->default(false)->nullable(false)
                ->comment('if true then this attribute will have different values per set. Otherwise an element value of this will not change when in different sets');


            //todo rm
            $table->boolean('is_access_type_editor')->default(false)->nullable(false)
                ->comment('if true then this attribute can only be read or written by the namespace admin group of the type not the child type');

            //todo rm
            $table->boolean('is_access_element_owner')->default(false)->nullable(false)
                ->comment('if true then this attribute can only be read or written by the owner of the element or those who have membership in it');
        });
        //todo type_approval_status new column

        /*
         * todo add in type_of_server_access enum
         * todo add in type_of_attribute_access enum
         */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_hordes');
    }
};
