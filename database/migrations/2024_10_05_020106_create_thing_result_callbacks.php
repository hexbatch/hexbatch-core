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
        Schema::create('thing_result_callbacks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('thing_result_id')
                ->nullable(false)
                ->comment("Belongs to this result")
                ->index()
                ->constrained('thing_results')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('caller_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("The namespace using this polling or callback")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('http_code_callback')->nullable()->default(null)
                ->comment('When the callback was made, what was the http code from that url');

            $table->timestamps();


        });


        DB::statement("CREATE TYPE type_thing_callback_status AS ENUM (
            'no_followup',
            'direct_followup',
            'polled_followup',
            'followup_callback_successful',
            'followup_callback_error'
            );");

        DB::statement("ALTER TABLE thing_result_callbacks Add COLUMN thing_callback_status type_thing_callback_status NOT NULL default 'no_followup';");

        Schema::table('thing_result_callbacks', function (Blueprint $table) {

            $table->string('result_callback_url')->nullable()->default(null)
                ->comment('If set, this will be called with the result or error');
        });


        DB::statement("ALTER TABLE thing_result_callbacks ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON thing_result_callbacks FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thing_result_callbacks');
        DB::statement("DROP TYPE type_thing_callback_status;");
    }
};
