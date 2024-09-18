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
    {//type,attribute,is_static,is_final
        Schema::create('element_type_attributes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('holder_element_type_id')
                ->nullable()->default(null)
                ->comment("The type holding the attribute")
                ->index('idx_element_type_holder_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('holdee_attribute_id')
                ->nullable()
                ->default(null)
                ->comment("The attribute being held, more than one type can hold the same attribute")
                ->index('idx_holdee_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->boolean('is_static')->default(false)->nullable(false)
                ->comment('if true then all elements share this static value. This is per server');

            $table->boolean('is_final')->default(false)->nullable(false)
                ->comment('if true then child types do not inherit this attribute');

            $table->boolean('is_private')->default(false)->nullable(false)
                ->comment('if true then only on read and written to by element owner, token owner if different cannot read/write');

        });

        DB::statement("ALTER TABLE element_type_attributes ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER element_type_attributes BEFORE UPDATE ON element_type_parents FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement("CREATE TYPE type_of_server_access AS ENUM (
            'public',
            'private_to_home_server',
            'whitelisted_servers',
            'whitelisted_servers_read_only',
            'other_servers_read_only'
            );");

        DB::statement("ALTER TABLE element_type_attributes Add COLUMN server_access_type type_of_server_access NOT NULL default 'private_to_home_server';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_attributes');
        DB::statement("DROP TYPE type_of_server_access;");
    }
};
