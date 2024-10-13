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
        Schema::create('server_events', function (Blueprint $table) {
            $table->id();



            $table->foreignId('event_trigger_type_id')
                ->nullable(false)
                ->comment("The event that this imported type will call")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('event_server_id')
                ->nullable()->default(null)
                ->comment("null for this server, otherwise send out this event to that server")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('event_target_type_id')
                ->nullable()->default(null)
                ->comment("The target type which includes descendants, can be null to only listen to any attribute")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('event_target_attribute_id')
                ->nullable(false)
                ->comment("The target attribute which includes descendants, must be specified")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('owning_attribute_id')
                ->nullable(false)
                ->comment("The attribute that owns this event, one attribute per event rule chain")
                ->unique()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_listening_before')
                ->nullable(false)->default(true)
                ->comment("If true then the rules can block this event, if after then this is run after the event successfully completes");

            $table->boolean('is_clipped_shape')
                ->nullable(false)->default(false)
                ->comment("If true then for events scoped to the same set this only listens if the attribute bounds intersect");


            $table->timestamps();

        });

        DB::statement("ALTER TABLE server_events ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON server_events FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement("CREATE TYPE type_of_server_event_access AS ENUM (
            'use_handler',
            'forbidden_event'
            );");

        DB::statement("ALTER TABLE server_events Add COLUMN event_access type_of_server_event_access NOT NULL default 'use_handler';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TYPE type_of_server_event_access;");

        Schema::dropIfExists('server_events');
    }
};
