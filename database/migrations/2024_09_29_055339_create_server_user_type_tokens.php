<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('server_user_type_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('to_server_user_type')
                ->nullable()
                ->default(null)
                ->comment("The owner of this type")
                ->index('idx_to_server_user_type')
                ->constrained('user_types')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('to_server_id')
                ->nullable()
                ->default(null)
                ->comment("The server this user belongs to, null means this one")
                ->index('idx_to_server_id')
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('user_server_token')
                ->nullable()->default(null)
                ->comment("the token the server sends for that user");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_user_type_tokens');
    }
};
