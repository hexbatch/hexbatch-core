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
        Schema::create('thing_sets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('set_thing_id')
                ->nullable()->default(null)
                ->comment("Set belong to this thing")
                ->index()
                ->constrained('things')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('set_attribute_id')
                ->nullable()->default(null)
                ->comment("Set has an attribute ")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('set_type_id')
                ->nullable()->default(null)
                ->comment("Set has a type")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('set_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("Set has a namespace")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('set_user_id')
                ->nullable()
                ->default(null)
                ->comment("Set has a user")
                ->index()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thing_sets');
    }
};
