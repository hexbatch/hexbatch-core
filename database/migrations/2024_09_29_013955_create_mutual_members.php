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
        Schema::create('mutual_members', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // todo create this table: mutual owner, element A from, element B to, optional Set A, B if this is live (code will update these )
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutual_members');
    }
};
