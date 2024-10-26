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
        Schema::create('thing_hook_clusters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('hooked_thing_id')
                ->nullable()->default(null)
                ->comment("The thing that has the hook enabled for itself and maybe down-thing")
                ->index()
                ->constrained('things')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('owning_thing_hook_id')
                ->nullable()->default(null)
                ->comment("when there is some hook(s) to run here")
                ->index()
                ->constrained('thing_hooks')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            $table->integer('hook_http_status')->default(null)->nullable()
                ->comment('when calling the hook, or having it set, there is a status for it');



            $table->timestamps();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");


            $table->jsonb('hook_data')
                ->nullable()->default(null)->comment("what the callback returned, also contains any error data from url");


        });

        DB::statement("CREATE TYPE type_hooked_thing_status AS ENUM (
            'none',
            'waiting_for_thing',
            'waiting_for_hook',
            'waiting_for_update',
            'callback_error',
            'hook_complete',
            'hook_complete_with_error',
            'hook_success',
            'hook_fail'


            );");

        DB::statement("ALTER TABLE thing_hook_clusters Add COLUMN hooked_thing_status type_hooked_thing_status NOT NULL default 'none';");

        DB::statement("ALTER TABLE thing_hook_clusters ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON thing_hook_clusters FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement('ALTER TABLE thing_hook_clusters ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thing_hook_clusters');
        DB::statement("DROP TYPE type_hooked_thing_status;");
    }
};
