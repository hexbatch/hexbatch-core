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
        Schema::create('mutuals', function (Blueprint $table) {
            $table->id();


            $table->foreignId('containing_set_id')
                ->nullable()->default(null)
                ->comment("the set which holds the mutual elements")
                ->index('idx_containing_set_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();



            $table->string('mutual_name',128)->nullable()->default(null)
                ->comment("The optional name of the mutual, using the naming rules");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutuals');
    }
};
