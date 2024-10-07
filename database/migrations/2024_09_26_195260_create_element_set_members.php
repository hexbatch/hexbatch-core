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
        Schema::create('element_set_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('holder_set_id')
                ->nullable(false)
                ->comment("The set that is holding the element")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('held_element_id')
                ->nullable()->default(null)
                ->comment("This element belongs to its set")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->unique(['holder_set_id','held_element_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_set_members');
    }
};
