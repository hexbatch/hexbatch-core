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


            $table->foreignId('server_user_type_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the element")
                ->index('idx_server_user_type_id')
                ->constrained('user_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('server_type_id')
                ->nullable()->default(null)
                ->comment("The type made for this server")
                ->index('idx_server_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

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
