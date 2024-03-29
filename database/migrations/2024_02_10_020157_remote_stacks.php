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
        Schema::create('remote_stacks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the stack")
                ->index('idx_remote_stack_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('parent_remote_stack_id')
                ->nullable()
                ->default(null)
                ->comment("if it has a parent executed first")
                ->index('idx_parent_remote_stack_id')
                ->constrained('remote_stacks')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('shortcut_stack_id')
                ->nullable()
                ->default(null)
                ->comment("if there is another stack that runs earlier and does the same thing, put it here, and we use that ending_data to avoid recursion but allow nested usages")
                ->index('idx_shortcut_remote_stack_id')
                ->constrained('remote_stacks')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->integer('level_from_top')->default(0)->nullable(false)
                ->comment('how many levels down are we?');


            $table->jsonb('children_data')->default(null)->nullable()
                ->comment('final data of any children after all is done');

            $table->jsonb('ending_data')->default(null)->nullable()
                ->comment('final data after processing, starting data is defined at each activity linked to this');

            $table->timestamps();
            $table->dateTime('stack_ended_at')->nullable()->default(null)->comment("filled in when this stack completes");

            $table->integer('child_priority_level')->nullable(false)->default(0)
                ->comment("when multiple children stacks, this determines the order of combining");
        });
        DB::statement("CREATE TYPE type_of_remote_stack_status AS ENUM (
            'none','pending','started','success','failed'
            );");

        DB::statement("ALTER TABLE remote_stacks Add COLUMN remote_stack_status_type type_of_remote_stack_status NOT NULL default 'none';");


        #--------------------------------------------
        DB::statement("CREATE TYPE type_of_remote_stack_call_merge AS ENUM (
            'all_must_succeed','some_failing_ok'
            );");

        DB::statement("ALTER TABLE remote_stacks Add COLUMN remote_stack_call_type type_of_remote_stack_call_merge NOT NULL default 'all_must_succeed';");


        DB::statement('ALTER TABLE remote_stacks ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE remote_stacks ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remote_stacks FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
        //todo add trigger in other file, and use it here, to detect any possible repeats of the same remote, with same cache callers, and set the later's activity cache_policy_type to cache or cache_or_fail policy
        //todo add trigger to see if any repeat usage of the same action, in the same element, and if so, set shortcut_stack_id here
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_stacks');
        DB::statement("DROP TYPE type_of_remote_stack_status");
        DB::statement("DROP TYPE type_of_remote_stack_call_merge");
    }
};
