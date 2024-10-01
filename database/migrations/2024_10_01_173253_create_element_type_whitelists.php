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
        Schema::create('element_type_whitelists', function (Blueprint $table) {
            $table->id();

            $table->foreignId('whitelist_owning_type_id')
                ->nullable()
                ->default(null)
                ->comment("What this whitelist is about")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('whitelist_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("The namespace this is about")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->integer('max_allowed')
                ->nullable()->default(null)->comment("If set, cannot do more than");

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");
        });

        DB::statement('ALTER TABLE element_type_whitelists ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE element_type_whitelists ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_type_whitelists FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement("CREATE TYPE type_of_whitelist_permission AS ENUM (
            'inheriting',
            'create_elements',
            'own_elements',
            'read_elements',
            'write_elements'
            );");

        DB::statement("ALTER TABLE element_type_whitelists Add COLUMN whitelist_permission type_of_whitelist_permission NOT NULL default 'inheriting';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_whitelists');
        DB::statement("DROP TYPE type_of_whitelist_permission;");
    }
};
