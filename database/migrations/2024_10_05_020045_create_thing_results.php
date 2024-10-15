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
        Schema::create('thing_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('result_thing_id')
                ->nullable(false)
                ->comment("Results belong to this thing")
                ->index()
                ->constrained('things')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('result_to_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("The namespace getting the result")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('result_hex_error_id')
                ->nullable()
                ->default(null)
                ->comment("When something goes wrong")
                ->index('idx_thing_hex_error_id')
                ->constrained('hex_errors')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->integer('result_callback_status')->nullable()->default(null)
                ->comment('When the callback was made, what was the code returned. ".
                "Use http codes for polling that is done before or after the thing finishes');


        });

        DB::statement("CREATE TYPE type_api_followup AS ENUM (
            'no_followup',
            'direct_followup',
            'polled_followup',
            'followup_callback_successful',
            'followup_callback_error'
            );");

        DB::statement("ALTER TABLE thing_results Add COLUMN user_followup type_api_followup NOT NULL default 'no_followup';");


        Schema::table('thing_results', function (Blueprint $table) {

            $table->string('result_callback_url')->nullable()->default(null)
                ->comment('If set, this will be called with the result or error');

            //(determined by class that gathers this thing and makes response)
            $table->jsonb('result_response')
                ->nullable()->default(null)->comment("When something needs a value");

        });

        DB::statement("ALTER TABLE thing_results ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON thing_results FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thing_results');
        DB::statement("DROP TYPE type_api_followup;");
    }
};
