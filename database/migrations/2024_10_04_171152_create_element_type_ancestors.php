<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** mmep
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('element_type_ancestors', function (Blueprint $table) {
            $table->id();
            //A , B, Gap where A is every type and B is one row for each entire ancestor chain, and Gap is how many generations

            $table->foreignId('owning_child_type_id')
                ->nullable(false)
                ->comment("The type which has parents and/or ancestors")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('ancestor_type_id')
                ->nullable(false)
                ->comment("The type which is the parent or ancestor")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('type_gap')->nullable(false)
                ->comment('How many generations apart');
        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_owning_ancestor_type ON element_type_ancestors (owning_child_type_id,ancestor_type_id) NULLS NOT DISTINCT;");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_ancestors');

    }
};
