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








        });
        DB::statement("ALTER TABLE element_type_parents Add COLUMN attribute_approval type_of_approval NOT NULL default 'automatic';");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_hordes');
    }
};
