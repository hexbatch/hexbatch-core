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
        Schema::create('thing_data', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owning_thing_id')
                ->nullable()->default(null)
                ->comment("Set belong to this thing")
                ->index()
                ->constrained('things')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_attribute_id')
                ->nullable()->default(null)
                ->comment("Set has an attribute ")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_type_id')
                ->nullable()->default(null)
                ->comment("Set has a type")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_set_id')
                ->nullable()->default(null)
                ->comment("Set has a set")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_element_id')
                ->nullable()->default(null)
                ->comment("Set has an element")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("Set has a namespace")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_cursor')
                ->nullable(false)->default(false)
                ->comment("If true what is in this row is the cursor for the path of the thing for the next page");


        });

        DB::statement("ALTER TABLE thing_data ADD CONSTRAINT chk_one_thing_set CHECK (
            num_nonnulls(collection_attribute_id  ,collection_type_id, collection_namespace_id, collection_element_id, collection_set_id) = 1)
        ;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thing_data');
    }
};
