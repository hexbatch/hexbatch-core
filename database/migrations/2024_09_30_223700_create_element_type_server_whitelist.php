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
        Schema::create('element_type_server_whitelist', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_whitelist_type_id')
                ->nullable()->default(null)
                ->comment("The type that has a whitelist entry")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('to_server_id')
                ->nullable()
                ->default(null)
                ->comment("The server added to the whitelist")
                ->index()
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });

        DB::statement("ALTER TABLE element_type_server_whitelist ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_type_server_whitelist FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement("CREATE TYPE type_of_server_whitelist AS ENUM (
            'server_access',
            'server_can_create_elements'
            );");

        DB::statement("ALTER TABLE element_type_server_whitelist Add COLUMN server_whitelist type_of_server_whitelist NOT NULL default 'server_access';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_server_whitelist');
        DB::statement("DROP TYPE type_of_server_whitelist;");
    }
};
