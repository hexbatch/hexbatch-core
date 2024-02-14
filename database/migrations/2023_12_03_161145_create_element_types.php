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
        //just make a stub
        Schema::create('element_types', function (Blueprint $table) {
            $table->id();


            $table->foreignId('user_id')
                ->nullable()
                ->default(null)
                ->comment("The owner")
                ->index('idx_element_type_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();


        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_types');
    }
};
