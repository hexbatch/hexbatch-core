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
        Schema::create('thing_results', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        //todo put the following in a new table
        //            * thing id
        //            * type_user_followup
        //            * url to callback
        //            * when user follow up
        //            * callback_status
        //            * response json   (determined by class that gathers this thing and makes response)
        //          *  error id
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thing_results');
    }
};
