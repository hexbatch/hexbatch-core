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
        Schema::create('remote_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remote_id')
                ->nullable(false)
                ->comment("The remote this header/key is for")
                ->index('idx_header_log_has_remote_id')
                ->constrained('remotes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('caller_action_id')
                ->nullable()
                ->default(null)
                ->comment("The action that called this remote")
                ->index('idx_remote_activities_action_id')
                ->constrained('actions')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('caller_attribute_id')
                ->nullable()
                ->default(null)
                ->comment("The attribute that called this remote")
                ->index('idx_remote_activities_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('caller_user_id')
                ->nullable()
                ->default(null)
                ->comment("The user that called this remote")
                ->index('idx_remote_activities_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('caller_element_id')
                ->nullable()
                ->default(null)
                ->comment("The element that called this remote")
                ->index('idx_remote_activities_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('caller_type_id')
                ->nullable()
                ->default(null)
                ->comment("The type that called this remote")
                ->index('idx_remote_activities_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->timestamps();
            $table->dateTime('remote_call_ended')->nullable()->default(null)->comment("filled in when remote completes");

        });

        DB::statement("CREATE TYPE type_of_remote_status AS ENUM (
            'none','pending','started','success','failed'
            );");

        DB::statement("ALTER TABLE remote_activities Add COLUMN status_type type_of_remote_status NOT NULL default 'none';");

        DB::statement('ALTER TABLE remote_activities ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');
        DB::statement("ALTER TABLE remote_activities ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remote_activities FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('remote_activities', function (Blueprint $table) {
            $table->index(['remote_id','status_type'],'idx_remotes_status');
            $table->integer('response_code')->nullable()->comment("the http status or console status");
            $table->jsonb('to_headers')->nullable()->comment("The headers to the remote (if that kind), no secret values here");
            $table->jsonb('from_headers')->nullable()->comment("The headers from the remote answering (if that kind), no secret values here");
            $table->jsonb('from_remote_processed_data')->nullable()->comment("The value of going in, if marked is_secret not put here");
            $table->jsonb('to_remote_processed_data')->nullable()->comment("The value coming back, if its json");
            $table->jsonb('errors')->nullable()->comment("Any errors from or to");
            $table->text('from_remote_raw_text')->nullable()->comment("The value coming back, if its not json (xml,html,primitive");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_activities');
        DB::statement("DROP TYPE type_of_remote_status");
    }
};
