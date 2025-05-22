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
        Schema::create('action_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_action_data_id')
                ->nullable()
                ->default(null)
                ->comment("The action data that owns this collection")
                ->index()
                ->constrained('action_data')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();




            $table->foreignId('collection_attribute_id')
                ->nullable()->default(null)
                ->comment("Collection has an attribute ")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_type_id')
                ->nullable()->default(null)
                ->comment("Collection has a type")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_set_id')
                ->nullable()->default(null)
                ->comment("Collection has a set")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_element_id')
                ->nullable()->default(null)
                ->comment("Collection has an element")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_set_member_id')
                ->nullable()->default(null)
                ->comment("Collection has a set member")
                ->index()
                ->constrained('element_set_members')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('collection_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("Collection has a namespace")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_path_id')
                ->nullable()
                ->default(null)
                ->comment("Collection has a path")
                ->index()
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_user_id')
                ->nullable()
                ->default(null)
                ->comment("Collection has a user")
                ->index()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('collection_server_id')
                ->nullable()
                ->default(null)
                ->comment("Collection has a server")
                ->index()
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('collection_mutual_id')
                ->nullable()
                ->default(null)
                ->comment("Collection has a mutual")
                ->index()
                ->constrained('mutuals')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('collection_phase_id')
                ->nullable()
                ->default(null)
                ->comment("Collection has a phase")
                ->index()
                ->constrained('phases')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('collection_partition_flag')->nullable(false)->default(0)
                ->comment("When need to do sub collections for the same type");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_collections');
    }
};
