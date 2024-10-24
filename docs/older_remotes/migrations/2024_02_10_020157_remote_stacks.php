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



            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->integer('level_from_top')->default(0)->nullable(false)
                ->comment('how many levels down are we?');


            $table->jsonb('children_data')->default(null)->nullable()
                ->comment('final merged data of any children stacks after they are all run');

            $table->jsonb('ending_activity_data')->default(null)->nullable()
                ->comment('merged data of any activities of this stack after they are all completed ');

            $table->jsonb('starting_activity_data')->default(null)->nullable()
                ->comment('any starting activity data that all activities directly attached to this get. If not main type, this is merged with the parent completed data');

            $table->jsonb('ending_data')->default(null)->nullable()
                ->comment('final merged data from main activites and children stacks. This is what is given to the success, or parent, if any. This is null on error');

            $table->jsonb('error_data')->default(null)->nullable()
                ->comment('if exception thrown in the stack logic, it is here');

            $table->timestamps();
            $table->dateTime('stack_ended_at')->nullable()->default(null)->comment("filled in when this stack completes");

            $table->integer('child_priority_level')->nullable(false)->default(0)
                ->comment("when multiple children stacks, this determines the order of combining, but not running");
        });

        # ----------------------------

        # non-main stuff get passed in the merged main return data that ran (including fails),  and only runs after main, which depends on the main logic
        DB::statement("CREATE TYPE type_of_remote_stack_category AS ENUM (
            'main','on_success','on_failure','on_always'
            );");

        DB::statement("ALTER TABLE remote_stacks Add COLUMN remote_stack_category type_of_remote_stack_category NOT NULL default 'main';");


        # ----------------------------

        DB::statement("CREATE TYPE type_of_remote_stack_status AS ENUM (
            'none','pending','started','success','failed','error'
            );");

        DB::statement("ALTER TABLE remote_stacks Add COLUMN remote_stack_status type_of_remote_stack_status NOT NULL default 'none';");


        #--------------------------------------------

        DB::statement("CREATE TYPE type_of_remote_stack_logic AS ENUM (
            'all_must_succeed','some_failing_ok'
            );");

        DB::statement("ALTER TABLE remote_stacks Add COLUMN remote_stack_logic_type type_of_remote_stack_logic NOT NULL default 'all_must_succeed';");



        #--------------------------------------------

        DB::statement('ALTER TABLE remote_stacks ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE remote_stacks ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remote_stacks FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_stacks');
        DB::statement("DROP TYPE type_of_remote_stack_status");
        DB::statement("DROP TYPE type_of_remote_stack_logic");
        DB::statement("DROP TYPE type_of_remote_stack_category");
    }
};
