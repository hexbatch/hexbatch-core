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
        //todo add trigger to change the working membership anytime the parent or this changes the defined membership
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       //todo drop triggers
    }
};
