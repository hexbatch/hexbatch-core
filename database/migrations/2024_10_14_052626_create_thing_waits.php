<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * thing_id, waiting_on_type, waiting_on_element, expires_at, enum thing_waiting_type wait_all|wait_one, timestamps
     */
    public function up(): void
    {
        Schema::create('thing_waits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('waiting_thing_id')
                ->nullable(false)
                ->comment("The thing that is waiting on the event or element")
                ->index()
                ->constrained('things')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('waiting_on_type_id')
                ->nullable(false)
                ->comment("Waiting on a type, can wait on more than one type at a time")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            //todo change to element
            $table->foreignId('waiting_with_set')
                ->nullable()->default(null)
                ->comment("The type may have a specific element that is ready, and not just any element to be ready")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->dateTime('expires_at')->nullable()->default(null)
                ->comment('If set, the wait will be stopped sometime after if conditions not met');


            $table->timestamps();
        });

        DB::statement("CREATE TYPE type_of_thing_wait_policy AS ENUM (
            'wait_all',
            'wait_one'
            );");

        DB::statement("ALTER TABLE thing_waits Add COLUMN thing_waiting_policy type_of_thing_wait_policy NOT NULL default 'wait_all';");

        DB::statement("ALTER TABLE thing_waits ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON thing_waits FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thing_waits');
        DB::statement("DROP TYPE type_of_thing_wait_policy;");
    }
};
