<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     *
     */
    public function up(): void
    {
        Schema::create('element_server_exports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('export_server_id')
                ->nullable()->default(null)
                ->comment("The server we sent this too")
                ->index()
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('export_type_id')
                ->nullable(false)
                ->comment("We exported this type, if the element goes away we still have a record of the type. For each different type, have a null element once")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('export_element_id')
                ->nullable()->default(null)
                ->comment("The element that was sent")
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_server_export_element_per_type ON element_server_exports (export_type_id,export_element_id) NULLS DISTINCT;");

        DB::statement("ALTER TABLE element_server_exports ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_server_exports FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_server_exports');
    }
};
