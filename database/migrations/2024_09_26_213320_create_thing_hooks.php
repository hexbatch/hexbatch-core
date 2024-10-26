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
        Schema::create('thing_hooks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owning_hook_cluster_id')
                ->nullable()->default(null)
                ->comment("when there is some hook(s) to run here")
                ->index()
                ->constrained('thing_hook_clusters')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_on')->default(true)->nullable(false)
                ->comment('if false then this hook is not used');


            $table->string('debugging_callback_url')->nullable()->default(null)
                ->comment('If set, this will be called with the result or error');

            //(determined by class that gathers this thing and makes response)
            $table->jsonb('extra_data')
                ->nullable()->default(null)->comment("When running multiple debuggers");
        });

        DB::statement("CREATE TYPE type_thing_hook_mode AS ENUM (
            'none',
            'debug_breakpoint',
            'tree_creation_hook',
            'tree_starting_hook',
            'tree_paused_hook',
            'tree_unpaused_hook',
            'tree_finished_hook',
            'tree_success_hook',
            'tree_failure_hook',
            'node_before_running_hook',
            'node_after_running_hook',
            'node_failure_hook',
            'node_success_hook'

            );");

        DB::statement("ALTER TABLE thing_hooks Add COLUMN thing_hook_mode type_thing_hook_mode NOT NULL default 'none';");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thing_hooks');
        DB::statement("DROP TYPE type_thing_hook_mode;");
    }
};
