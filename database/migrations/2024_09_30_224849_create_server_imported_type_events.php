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
        Schema::create('server_imported_type_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_import_id')
                ->nullable()->default(null)
                ->comment("The imported type")
                ->index()
                ->constrained('server_imported_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('imported_event_type_id')
                ->nullable()->default(null)
                ->comment("The event that this imported type will call")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });

        DB::statement("ALTER TABLE server_imported_type_events ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON server_imported_type_events FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_imported_type_events');
    }
};
