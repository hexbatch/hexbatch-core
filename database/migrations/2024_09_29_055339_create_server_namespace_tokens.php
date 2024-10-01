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
        Schema::create('server_namespace_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('token_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("The namespace this is about")
                ->index('idx_token_namespace_id')
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('to_server_id')
                ->nullable()
                ->default(null)
                ->comment("The server this namespace is registered at, null means this server")
                ->index('idx_to_server_id')
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();


            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->string('user_server_token')
                ->nullable()->default(null)
                ->comment("the token used");

            $table->timestamps();


        });

        DB::statement("ALTER TABLE server_namespace_tokens ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON server_namespace_tokens FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_namespace_tokens');
    }
};
