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
        Schema::create('system_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_alias_type_id')
                ->nullable()->default(null)
                ->comment("If this alias is about a type")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('system_alias_attribute_id')
                ->nullable()->default(null)
                ->comment("The target attribute which includes descendants, must be specified")
                ->index()
                ->constrained('if this alias is about an attribute')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->string('system_alias_iso_language',10)->index();

            $table->string('system_alias_name',
                (\App\Sys\SystemResources::MAX_SYSTEM_RESOURCE_NAME_LENGTH + 1) * \App\Sys\SystemResources::MAX_SYSTEM_RESOURCE_NESTING)
                ->index();

            $table->unique(['system_alias_type_id','system_alias_attribute_id','system_alias_iso_language','system_alias_name']);
        });

        DB::statement('ALTER TABLE system_aliases ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE system_aliases ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON system_aliases FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_aliases');
    }
};
