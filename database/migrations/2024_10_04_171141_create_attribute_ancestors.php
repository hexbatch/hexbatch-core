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
        Schema::create('attribute_ancestors', function (Blueprint $table) {
            $table->id();
            // A , B, Gap where A is every attribute and B is one row for each entire ancestor chain, and Gap is how many generations

            $table->foreignId('child_attribute_id')
                ->nullable(false)
                ->comment("The attribute which has parents and/or ancestors")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('ancestor_attribute_id')
                ->nullable(false)
                ->comment("The attribute which is the parent or ancestor")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('attribute_gap')->nullable(false)
                ->comment('How many generations apart');
        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_owning_ancestor_attribute ON attribute_ancestors (child_attribute_id,ancestor_attribute_id) NULLS NOT DISTINCT;");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_ancestors');
    }
};
