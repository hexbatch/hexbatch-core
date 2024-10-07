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
                ->nullable(true)->default(null)
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

            $table->timestamps();

        });

        DB::statement("ALTER TABLE server_events ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON server_events FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_events');
    }
};
