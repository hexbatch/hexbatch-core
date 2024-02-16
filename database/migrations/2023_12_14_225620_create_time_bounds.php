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

        Schema::create('time_bounds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the bound")
                ->index('idx_time_bound_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->timestamps();

            $table->boolean('is_retired')->default(false)->nullable(false)
                ->comment('if true then cannot be added to token types or make new tokens');

            $table->dateTime('bound_start')->nullable(false)
                ->comment('When this time bound starts (inclusive)');

            $table->dateTime('bound_stop')->nullable(false)
                ->comment('When this time bound stops (inclusive)');

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->integer('bound_period_length')->default(null)->nullable()
                ->comment("The period length when a cron expression is used. Required if cron expression");

            $table->string('bound_cron',64)->nullable()->default(null)
                ->comment("The rule to make time bound spans");

            $table->string('bound_cron_timezone',35)->nullable()->default(null)
                ->comment("If provided, the cron generated spans will use this timezone");


            $table->string('bound_name',128)->nullable(false)->index()
                ->comment("The unique name of the time bound, using the naming rules");



            $table->unique(['user_id','bound_name']);
        });

        DB::statement('ALTER TABLE time_bounds ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE time_bounds ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON time_bounds FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");


        DB::statement('ALTER TABLE time_bounds ADD CONSTRAINT time_bound_start_before_stop_constraint CHECK (bound_start < bound_stop)');
        DB::statement('ALTER TABLE time_bounds ADD CONSTRAINT bound_period_length_positive_constraint CHECK (bound_period_length > 0 OR bound_period_length IS NULL)');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_bounds');
    }
};
