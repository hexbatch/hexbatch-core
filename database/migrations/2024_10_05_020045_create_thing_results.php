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

            $table->foreignId('owner_thing_id')
                ->nullable(false)
                ->comment("Results belong to this thing")
                ->index()
                ->constrained('things')
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


            $table->integer('result_http_status')->nullable()->default(null)
                ->comment('if not set, then incomplete, else the code for the finished work');

            $table->timestamps();

        });






        Schema::table('thing_results', function (Blueprint $table) {


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

    }
};
