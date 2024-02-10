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
        Schema::create('remote_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remote_id')
                ->nullable(false)
                ->comment("The remote this header/key is for")
                ->index('idx_header_log_has_remote_id')
                ->constrained('remotes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->timestamps();
            $table->dateTime('remote_call_ended')->nullable()->default(null)->comment("filled in when remote completes");

        });

        DB::statement("ALTER TABLE remote_logs ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remote_logs FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('remote_logs', function (Blueprint $table) {
            $table->integer('http_response_code')->nullable()->comment("the http status or console status");
            $table->jsonb('headers')->nullable()->comment("The headers going and coming to the remote (if that kind), no secret values here");
            $table->jsonb('input_data')->nullable()->comment("The value of going in, if marked is_secret not put here");
            $table->jsonb('output_data')->nullable()->comment("The value coming back, if its json");
            $table->text('output_data_text')->nullable()->comment("The value coming back, if its not json (xml,html,primitive");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_logs');
    }
};
