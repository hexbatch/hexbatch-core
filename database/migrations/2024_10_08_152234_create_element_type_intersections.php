<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //todo make new table for type_intersections, which can be map or shape, for  the same set
    /*
     * these can be live or design types mixed together
     * todo
     *   add columns for set member
     *               existing type,
     *               entry type,
     *               and what type intersection this is, enclosed, enclosing,intersecting
     *              and if this is map or shape (use location type)
     */
    // pop for live types

    public function up(): void
    {
        Schema::create('element_type_intersections', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_intersections');
    }
};
