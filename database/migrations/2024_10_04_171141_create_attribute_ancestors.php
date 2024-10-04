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
        Schema::create('attribute_ancestors', function (Blueprint $table) {
            $table->id();
            //todo A , B, Gap where A is every attribute and B is one row for each entire ancestor chain, and Gap is how many generations
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_ancestors');
    }
};
