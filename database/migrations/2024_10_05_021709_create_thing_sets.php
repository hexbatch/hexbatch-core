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
            $table->timestamps();
            /*
             * thing_id
             * attribute_id
             * type_id
             * namespace_id
             * element_id
             * user_id
             */
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
