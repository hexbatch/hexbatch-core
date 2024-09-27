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
        Schema::create('server_types', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owning_server_id')
                ->nullable()->default(null)
                ->comment("The server that owns this type")
                ->unique('udx_owning_server_id')
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('server_type_id')
                ->nullable()->default(null)
                ->comment("Each server has its own type, table here casts it in other tables")
                ->unique('udx_server_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });

        DB::statement("ALTER TABLE server_types ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON server_types FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_types');
    }
};
