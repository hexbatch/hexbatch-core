<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        //just make a stub
        Schema::create('elements', function (Blueprint $table) {
            $table->id();



            $table->foreignId('element_type_id')
                ->nullable(false)
                ->comment("The parent type")
                ->index('idx_element_parent_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('user_id')
                ->nullable()
                ->default(null)
                ->comment("The owner")
                ->index('idx_element_user_id')
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
        Schema::dropIfExists('elements');
    }
};
