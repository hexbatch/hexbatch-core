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
        Schema::create('thing_setting_clusters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('setting_cluster_thing_id')
                ->nullable(false)
                ->comment("A thing uses this group of settings. Its children uses it too")
                ->index()
                ->constrained('things')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('owning_setting_id')
                ->nullable(false)
                ->comment("when there is setting here")
                ->index()
                ->constrained('thing_settings')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique(['setting_cluster_thing_id','owning_setting_id']);

        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thing_setting_clusters');
    }
};
