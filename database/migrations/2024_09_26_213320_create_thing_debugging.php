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
        Schema::create('thing_debugging', function (Blueprint $table) {
            $table->id();

            $table->boolean('is_primary')->default(false)->nullable(false)
                ->comment('if true then each thing is marked with this');


            $table->string('debugging_callback_url')->nullable()->default(null)
                ->comment('If set, this will be called with the result or error');

            //(determined by class that gathers this thing and makes response)
            $table->jsonb('extra_data')
                ->nullable()->default(null)->comment("When running multiple debuggers");
        });

        DB::statement("CREATE TYPE type_debugging_mode AS ENUM (
            'run_to_cursor',
            'single_step',
            'step_over',
            'off'
            );");

        DB::statement("ALTER TABLE thing_debugging Add COLUMN debugging_mode type_debugging_mode NOT NULL default 'off';");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thing_debugging');
        DB::statement("DROP TYPE type_debugging_mode;");
    }
};
