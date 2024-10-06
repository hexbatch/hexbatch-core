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
        Schema::create('element_type_hordes', function (Blueprint $table) {
            $table->id();


            $table->foreignId('horde_type_id')
                ->nullable(false)
                ->comment("The type that holds the attributes, which are selected when the type is created from the parents")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('originating_horde_id')
                ->nullable()->default(null)
                ->comment("The type that holds the attributes, which are selected when the type is created from the parents")
                ->index()
                ->constrained('element_type_hordes')
                ->restrictOnDelete()
                ->cascadeOnDelete();


            $table->foreignId('horde_attribute_id')
                ->nullable(false)
                ->comment("The attribute that belongs to the type")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique(['horde_type_id','horde_attribute_id']);








        });
        DB::statement("ALTER TABLE element_type_hordes Add COLUMN attribute_approval type_of_approval NOT NULL default 'automatic';");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_hordes');
    }
};
