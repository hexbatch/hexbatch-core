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
        Schema::create('element_type_server_levels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_access_type_id')
                ->nullable(false)
                ->comment("The type that has a whitelist entry")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('to_server_id')
                ->nullable()
                ->default(null)
                ->comment("The server added to the whitelist. If server is null, then this applies to all servers for the type")
                ->index()
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });

        DB::statement("ALTER TABLE element_type_server_levels ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_type_server_levels FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");


        //Ancestor types cannot increase access, only lessen it
        DB::statement("ALTER TABLE element_type_server_levels Add COLUMN access_type type_of_server_access NOT NULL default 'private_attribute';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_server_levels');
    }
};
