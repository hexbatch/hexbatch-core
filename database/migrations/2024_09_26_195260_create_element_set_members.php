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

            $table->foreignId('member_element_id')
                ->nullable()->default(null)
                ->comment("This element belongs to its set")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('member_rank')
                ->nullable(false)->default(0)
                ->comment("orders the elements");

            $table->boolean('is_sticky')
                ->nullable(false)->default(false)
                ->comment("If true when will stay when some commands empty out the set, or remove elements from the set");


            $table->unique(['holder_set_id','member_element_id']);
            $table->unique(['member_element_id','member_rank']);
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
